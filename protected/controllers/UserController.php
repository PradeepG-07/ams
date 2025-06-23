<?php
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;
 use MongoDB\BSON\ObjectId;
class UserController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow', // allow all users to perform 'index','register' and 'login' actions
                'actions' => array('index', 'register', 'login'),
                'users' => array('*'),
            ),
            array(
                'allow', // allow authenticated user to perform 'view', 'update' and 'delete' actions
                'actions' => array('view', 'update', 'delete'),
                'users' => array('*'),
            ),
            // array(
            //     'allow', // allow admin user to perform 'admin' and 'delete' actions
            //     'actions' => array('admin', 'delete'),
            //     'users' => array('admin'),
            // ),
            // array(
            //     'deny', // deny all users
            //     'users' => array('*'),
            // ),
        );
    }


    /**
     * Creating a new user
     */
    public function actionCreate()
    {
        try {
            Yii::log("Displaying user create", CLogger::LEVEL_INFO, 'application.controllers.UserController');
            $student = new Student();
            $model = new User();
            $teacher = new Teacher();

            // Validate the model before rendering
            $this->performAjaxValidation($model);
            if (isset($_POST['User']) && (isset($_POST['Student']) || isset($_POST['Teacher']))) {
                $model->attributes = $_POST['User'];
                if ($model->save()) {
                    if ($model->role == User::ROLE_STUDENT) {
                        if(empty($_POST['Student'])){
                            Yii::log("Student data is empty", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                            throw new CHttpException(400, 'Student data is required.');
                        }
                        $student->attributes = $_POST['Student'];
                        $student->user_id = $model->_id; // Assuming user_id is the foreign key in Student
                        if ($student->save()) {
                            $this->refresh();
                        } else {
                            $model->delete();
                        }
                    } elseif ($model->role == User::ROLE_TEACHER) {
                        if(empty($_POST['Teacher'])){
                            Yii::log("Teacher data is empty", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                            throw new CHttpException(400, 'Teacher data is required.');
                        }
                        $teacher->attributes = $_POST['Teacher'];
                        $teacher->user_id = $model->_id;
                        if ($teacher->save()) {
                            $this->refresh();
                        } else {
                            $model->delete();
                        }
                    } else {
                        throw new CHttpException(400, 'Invalid role specified.');
                    }
                }
            }
            $this->render('create', array(
                'model' => $model,
                'student' => $student,
                'teacher' => $teacher,
            ));
        } catch (Exception $e) {
            Yii::log("Error creating user: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.UserController');
            throw new CHttpException(500, 'An error occurred while creating user: ' . $e->getMessage());
        }
    }

    private function sendAjaxResponseIfAjax($students, $total)
    {
        if (Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode(array(
                'success' => true,
                'total' => $total,
                'students' =>  $students,
            ));
            Yii::app()->end();
        }
    }
    private function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }


    public function actionDelete($id){
        try {
            $id = new ObjectId($id);
            $user = UserHelper::loadUserById($id);
            if($user->role == User::ROLE_TEACHER){
                $tresult = TeacherHelper::deleteTeacherByUserId($id);
                if (!$tresult['success']) {
                    Yii::log("Failed to delete teacher with user ID: $id", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                    Yii::app()->user->setFlash('error', 'Failed to delete teacher: ' . $tresult['message']);
                    return;
                }

            } elseif($user->role == User::ROLE_STUDENT){
                $sresult = StudentHelper::deleteStudentByUserId($id);
                if (!$sresult['success']) {
                    Yii::log("Failed to delete student with user ID: $id", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                    Yii::app()->user->setFlash('error', 'Failed to delete student: ' . $sresult['message']);
                    return;
                }
            }
            else{
                Yii::log("Admin user deletion is not allowed", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                Yii::app()->user->setFlash('error', 'Admin user deletion is not allowed.');
                return;
            }
            $result = UserHelper::deleteUser($id);
            if ($result['success']) {
                Yii::log("User with ID: $id deleted successfully", CLogger::LEVEL_INFO, 'application.controllers.UserController');
                Yii::app()->user->setFlash('success', 'User deleted successfully.');
                echo CJSON::encode(array(
                    'success' => true,
                    'message' => 'User deleted successfully.'
                ));
            } else {
                Yii::log("Failed to delete user with ID: $id", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                Yii::app()->user->setFlash('error', 'Failed to delete user: ' . $result['message']);
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to delete user: ' . $result['message']
                ));
            }
        } catch (Exception $e) {
            Yii::log("Error deleting user: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.UserController');
            Yii::app()->user->setFlash('error', 'An error occurred while deleting the user: ' . $e->getMessage());
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'An error occurred while deleting the user: ' . $e->getMessage()
            ));
        }
    }

    public function actionUpdate($id){
        try{
            $user = UserHelper::loadUserById(new ObjectId($id));
            if (!$user) {
                throw new CHttpException(404, 'User not found.');
            }
            // $ = null;
            $student = new Student();
            $teacher = new Teacher();
            if($user->role == User::ROLE_TEACHER){
                $teacher = TeacherHelper::loadTeacherByUserId(new ObjectId($id));
                if (!$teacher) {
                    throw new CHttpException(404, 'Teacher not found.');
                }

            } elseif($user->role == User::ROLE_STUDENT){
                $student = StudentHelper::loadStudentByUserId(new ObjectId($id));
                if (!$student) {
                    throw new CHttpException(404, 'Student not found.');
                }
            } else {
                throw new CHttpException(400, 'Invalid role specified.');
            }
            $this->render('_form', array(
                'user' => $user,
                'student' => $student,
                'teacher' => $teacher,
                
            ));
            return;
        } catch (Exception $e) {
            Yii::log("Error updating user: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.UserController');
            throw new CHttpException(500, 'An error occurred while updating user: ' . $e->getMessage());
        }
   
    }
}
