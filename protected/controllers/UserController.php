<?php
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;
 
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
                'actions' => array('index','register','login'),
                'users' => array('*'),
            ),
            array(
                'allow', // allow authenticated user to perform 'view', 'update' and 'delete' actions
                'actions' => array('view', 'update','delete'),
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
        try{
            Yii::log("Displaying user create", CLogger::LEVEL_INFO, 'application.controllers.UserController');
            $student = new Student();
            $model = new User();
            $teacher = new Teacher();

            // Validate the model before rendering
            $this->performAjaxValidation($model);
            if (isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                // if ($model->validate()) {
                //     // Save the user and student data
                //     $model->save();
                //     $student->user_id = $model->id; // Assuming user_id is the foreign key in Student
                //     $student->save();

                //     // Redirect to the index page after successful creation
                //     $this->redirect(array('index'));
                // }
            }
            $this->render('create', array(
                'model' => $model,
                'student' => $student,
                'teacher' => $teacher,
            ));

        }catch(Exception $e){
            Yii::log("Error creating user: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.UserController');
            throw new CHttpException(500, 'An error occurred while creating user: ' . $e->getMessage());
        }
    }

    private function sendAjaxResponseIfAjax($students,$total)
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
   
   
}