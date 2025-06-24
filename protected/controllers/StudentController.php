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
class StudentController extends Controller
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
            'postOnly + delete',
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
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array(
                'allow',
                'actions' => array('dashboard','index','getAllStudentsofclass', 'stats', 'daywise', 'profile', 'getProfile', 'uploadProfilePicture', 'removeProfilePicture'),
                'users' => array('*'),
            ),
            array(
                'allow',
                'actions' => array('admin', 'delete'),
                'users' => array('*'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionDashboard(){
        $studentId = Yii::app()->user->getState('student_id');
        $studentId = new ObjectId($studentId); // Ensure studentId is an ObjectId
        $studentData = StudentHelper::getStudentWithClassAndUserPopulated($studentId);

        $this->render('dashboard', array(
            'student' => $studentData,
        ));
    }

    /**
     * Displays a list of all students
     */
    public function actionIndex($page = 1)
    {
        try{
            Yii::log("Displaying student index", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            $students = StudentHelper::listStudents($page);
            $total = StudentHelper::count();
            $this->sendAjaxResponseIfAjax($students,$total);
            // echo CJSON::encode(array(
            //     'success' => true,
            //     'total' => $total,
            //     'students' =>  $students,
            // ));
            $this->render('index', array(
                'students' => $students,
                'total' => $total,
            ));

        }catch(Exception $e){
            Yii::log("Error listing students: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.StudentController');
            throw new CHttpException(500, 'An error occurred while listing students: ' . $e->getMessage());
        }
    }

    public function actionGetAllStudentsOfClass($classId)
    {
        try {
            Yii::log("Fetching students for class ID: $classId", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            $classId = new ObjectId($classId); // Ensure classId is an ObjectId
            $students = StudentHelper::getStudentsFromClassName($classId);
            // print_r(CJSON::encode($students));
            // exit;
            $total = count($students);
            $this->sendAjaxResponseIfAjax($students, $total);
            echo CJSON::encode(array(
                'success' => true,
                'total' => $total,
                'students' =>  $students,
            ));
        } catch (Exception $e) {
            Yii::log("Error fetching students for class ID: $classId - " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.StudentController');
            throw new CHttpException(500, 'An error occurred while fetching students: ' . $e->getMessage());
        }
    }

    private function sendAjaxResponseIfAjax($students,$total)
    {
        if (Yii::app()->request->isAjaxRequest) {
            print_r(CJSON::encode(array(
                'success' => true,
                'total' => $total,
                'students' =>  $students,
            )));
            Yii::app()->end();
        }
    }


}