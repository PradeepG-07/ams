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
                'allow', 
                'actions' => array('create', 'update', 'delete'),
                'expression' => "Yii::app()->user->isAdmin()",
            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
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
            $classes = ClassesHelper::getAllClasses();

            // if(isset($_POST['User'])){
            //     print_r($_POST);
            //     exit;
            // }
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
                        $result = StudentHelper::createStudent($_POST['Student'], $model->_id);
                        $student = $result['model'];
                        if ($result['success']) {
                            $this->redirect(Yii::app()->createUrl('student/index'));
                        } else {
                            $model->delete();
                            throw new CHttpException(400, 'Failed to create student: ' . $result['message']);
                        }
                    } elseif ($model->role == User::ROLE_TEACHER) {
                        if(empty($_POST['Teacher'])){
                            Yii::log("Teacher data is empty", CLogger::LEVEL_WARNING, 'application.controllers.UserController');
                            throw new CHttpException(400, 'Teacher data is required.');
                        }
                        $result = TeacherHelper::createTeacher($_POST['Teacher'], $model->_id);
                        $teacher = $result['model'];
                        if ($result['success']) {
                            $this->redirect(Yii::app()->createUrl('teacher/index'));

                        } else {
                            $model->delete();
                            throw new CHttpException(400, 'Failed to create teacher: ' . $result['message']);   
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
                'classes' => $classes,
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
            $id = new ObjectId($id);
            $user = UserHelper::loadUserById($id);
            if(!$user){
                throw new CHttpException(404, 'User not found.');
            }
            Yii::log("Updating user with ID: $id", CLogger::LEVEL_INFO, 'application.controllers.UserController');
            $user->password = ''; // Reset password to empty string for security
            

            $classes = ClassesHelper::getAllClasses();
            $student = new Student();
            $teacher = new Teacher();

            if($user->role == User::ROLE_TEACHER){
                $teacher = TeacherHelper::loadTeacherByUserId($id);
                if (!$teacher) {
                    throw new CHttpException(404, 'Teacher not found.');
                }
            } elseif($user->role == User::ROLE_STUDENT){
                $student = StudentHelper::loadStudentByUserId($id);
                if (!$student) {
                    throw new CHttpException(404, 'Student not found.');
                }
            } else {
                throw new CHttpException(400, 'Invalid role specified.');
            }

            if (isset($_POST['User']) || isset($_POST['Student']) || isset($_POST['Teacher'])) {
                $updateResults = array();
                
                // Update User collection if User data is provided
                
                if (isset($_POST['User'])) {
                    $_POST['User']['role'] = $user->role;
                    $user->attributes = $_POST['User'];
                    if(empty($_POST['User']['password'])){
                        $user->password = $user->getOldPassword();
                    }
                    $updateResults['user'] = $user->save();
                }
                
                // Update Student collection if Student data is provided
                if (isset($_POST['Student']) && $user->role == User::ROLE_STUDENT) {
                    $result = StudentHelper::updateStudent($student->_id, $_POST['Student']);
                    $updateResults['student'] = $result['success'];
                    $student = $result['model']; // Update with validation errors if any
                }
                
                // Update Teacher collection if Teacher data is provided
                if (isset($_POST['Teacher']) && $user->role == User::ROLE_TEACHER) {
                    $result = TeacherHelper::updateTeacher($teacher->_id, $_POST['Teacher']);
                    $updateResults['teacher'] = $result['success'];
                    $teacher = $result['model'];
                }
                
                // Check if all updates were successful
                $allSuccessful = !in_array(false, $updateResults, true);
                
                if ($allSuccessful && !empty($updateResults)) {
                    Yii::app()->user->setFlash('success', 'User updated successfully.');
                    if($user->role == User::ROLE_STUDENT){
                        $this->redirect(Yii::app()->createUrl('student/index'));
                    } else{
                        $this->redirect(Yii::app()->createUrl('teacher/index'));
                    }
                } else {
                    // Some updates failed, form will be re-rendered with validation errors
                    Yii::app()->user->setFlash('error', 'Some updates failed. Please check the form for errors.');
                }
            }
            
        
            $this->render('_form', array(
                'user' => $user,
                'student' => $student,
                'teacher' => $teacher,
                'classes' => $classes,
            ));
            
        } catch (Exception $e) {
            Yii::log("Error updating user: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.UserController');
            throw new CHttpException(500, 'An error occurred while updating user: ' . $e->getMessage());
        }
    }

}
