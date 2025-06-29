<?php

use MongoDB\BSON\ObjectId;

/**
 * StudentController - Refactored to use helper classes for business logic
 * 
 * This controller now focuses solely on:
 * - Handling HTTP requests and responses
 * - Calling appropriate helper methods
 * - Managing view rendering
 * - Basic request validation
 */
class AttendanceController extends Controller
{
    /**
     * @var string the default layout for the views
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
            // 'postOnly + delete',
        );
    }

    /**
     * Specifies the access control rules
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'save', 'manage'),
                'expression' => "Yii::app()->user->isTeacher() || Yii::app()->user->isAdmin()",
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a list of all students
     */
    public function actionIndex($page = 1)
    {
        try{
            Yii::log("Displaying classes index", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
            $classes = ClassesHelper::listClasses($page);
            $total = ClassesHelper::count();
            $this->sendAjaxResponseIfAjax($classes,$total);
            // echo CJSON::encode(array(
            //     'success' => true,
            //     'total' => $total,
            //     'classes' =>  $classes,
            // ));
            // exit;
            $this->render('index', array(
                'classes' => $classes,
                'total' => $total,
            ));

        }catch(Exception $e){
            Yii::log("Error listing classes: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while listing classes: ' . $e->getMessage());
        }
    }
    public function actionSave(){
        // print_r($_POST);
        // check if date is <= today
        
        try {
            Yii::log("Saving attendance for class ID: " . $_POST['class_id'], CLogger::LEVEL_INFO, 'application.controllers.AttendanceController');
            $teacher_id = $_POST['teacher_id'] ?? null;
            // Validate class_id
            if(isset($teacher_id) && !empty($teacher_id) && !Yii::app()->user->isAdmin()){
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'You are not allowed to save attendance.'
                ));
                Yii::log("Unauthorized attempt to save attendance by user ID: " . Yii::app()->user->id, CLogger::LEVEL_WARNING, 'application.controllers.AttendanceController');
                return;
            }
            if(Yii::app()->user->isTeacher()){
                $teacher_id = Yii::app()->user->getState('teacher_id');
            }
            $result = AttendanceHelper::saveAttendance($_POST,$teacher_id);
            if ($result['success']) {
                echo CJSON::encode(array(
                    'success' => true,
                    'message' => $result['message']
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => $result['message']
                ));
            }
        } catch (Exception $e) {
            Yii::log("Error saving attendance: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.AttendanceController');
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'An error occurred while saving attendance: ' . $e->getMessage()
            ));
        }

    }

    public function actionManage(){
        Yii::log("Displaying manage attendance page", CLogger::LEVEL_INFO, 'application.controllers.AttendanceController');
        $classes = [];
        $teacherId = Yii::app()->user->getState('teacher_id');
        // print_r($teacherId);
        if(Yii::app()->user->isTeacher()){
            $teacher = TeacherHelper::getTeacherWithPopulatedClasses(new ObjectId(Yii::app()->user->getState('teacher_id')));
            if(!$teacher){
                throw new CHttpException(404, 'Teacher not found.');
            }
            // echo "<pre>";
            // print_r($teacher);
            // exit;
            $classes = [];
            // echo "<pre>";
            // print_r($teacher);
            // print_r($teacher->classes);
            // exit;
            foreach($teacher["classes"] as $class){
                $classes[(string)$class['_id']] = $class['class_name'];
            }
        }
        else if(Yii::app()->user->isAdmin()){
            $classes = ClassesHelper::getAllClasses();
        }
        else{
            throw new CHttpException(403, 'You are not allowed to access this page.');
        }
        // print_r($classes);
        // exit;
        $this->render('manage', array(
            'classes' => $classes,
        ));
    }
    
}