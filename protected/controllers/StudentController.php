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
                'actions' => array('dashboard', 'attendanceRange'),
                'expression' => "Yii::app()->user->isStudent()",
            ),
            array(
                'allow',
                'actions' => array('index', 'getAllStudentsOfClass'),
                'expression' => "Yii::app()->user->isAdmin()",
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionDashboard(){
        try {
            Yii::log("Processing student dashboard request", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            
            $studentId = Yii::app()->user->getState('student_id');
            Yii::log("Dashboard request for student ID: $studentId", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            
            $studentId = new ObjectId($studentId); // Ensure studentId is an ObjectId
            $studentData = StudentHelper::getStudentWithClassAndUserPopulated($studentId);
            $attendanceData = StudentHelper::calculateAttendance($studentId);
            
            Yii::log("Successfully retrieved student data and attendance for dashboard", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
        
            $this->render('dashboard', array(
                'student' => $studentData,
                'totalSessions' => $attendanceData['total_sessions'],
                'sessionsAttended' => $attendanceData['sessions_attended'],
                'attendancePercentage' => $attendanceData['attendance_percentage'],
            ));
            
        } catch (Exception $e) {
            Yii::log("Error in actionDashboard: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.StudentController');
            throw new CHttpException(500, 'An error occurred while loading the dashboard: ' . $e->getMessage());
        }
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

    public function actionAttendanceRange(){
        try {
            Yii::log("Processing attendance range request", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            
            $studentId = Yii::app()->user->getState('student_id');
            $fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : '';
            $toDate = isset($_GET['toDate']) ? $_GET['toDate'] : '';
            
            Yii::log("Attendance range parameters - studentId: $studentId, fromDate: $fromDate, toDate: $toDate", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            
            
            $attendanceData = StudentHelper::getAttendanceDataProvider($studentId, $fromDate, $toDate);
            
            if (Yii::app()->request->isAjaxRequest) {
                Yii::log("Rendering partial view for AJAX request", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
                $this->renderPartial('attendancegrid', array(
                    'attendanceDataProvider' => $attendanceData,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                ), false, true);
                Yii::app()->end();
            }
            
            Yii::log("Rendering full view for attendance range", CLogger::LEVEL_INFO, 'application.controllers.StudentController');
            $this->render('attendancerange', array(
                'attendanceDataProvider' => $attendanceData,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            ));
            
        } catch (Exception $e) {
            Yii::log("Error in actionAttendanceRange: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.StudentController');
            throw new CHttpException(500, 'An error occurred while processing attendance range: ' . $e->getMessage());
        }
    }
}