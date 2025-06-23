<?php

use MongoDB\BSON\ObjectId;

/**
 * TeacherController - Refactored to use helper classes for business logic
 * 
 * This controller now focuses solely on:
 * - Handling HTTP requests and responses
 * - Calling appropriate helper methods
 * - Managing view rendering
 * - Basic request validation
 */
class TeacherController extends Controller
{
    public $layout = '//layouts/column2';

    public function filters()
    {
        return array(
            'accessControl',
            'postOnly + delete',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index', 'view'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('classes', 'classDetails', 'attendance', 'login', 'saveAttendance'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('admin', 'delete'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Display teacher's classes
     */
    public function actionClasses()
    {
        try {
            // Get teacher ID from request or use first teacher for demo
            $teacherId = isset($_GET['id']) ? $_GET['id'] : null;

            if ($teacherId) {
                // Validate teacher ID format
                $idValidation = ValidationHelper::validateObjectId($teacherId, 'Teacher ID');
                if (!$idValidation['success']) {
                    throw new CHttpException(400, 'Invalid teacher ID format');
                }
            } else {
                // For demo purposes, get the first teacher
                $teacher = Teacher::model()->find();
                if (!$teacher) {
                    throw new CHttpException(404, 'No teachers found');
                }
                $teacherId = (string)$teacher->_id;
            }

            // Get teacher classes using helper
            $classesResult = ClassroomHelper::getTeacherClasses($teacherId);
            if (!$classesResult['success']) {
                Yii::log('Failed to get teacher classes: ' . $classesResult['message'], 'error', 'TeacherController');
                throw new CHttpException(500, 'Unable to load teacher classes');
            }

            // Render the classes view
            $this->render('classes', array(
                'teacher' => $classesResult['data']
            ));

        } catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in teacher classes: ' . $e->getMessage(), 'error', 'TeacherController');
            throw new CHttpException(500, 'Unable to load classes');
        }
    }

    /**
     * Display detailed information about a specific class
     * 
     * @param string $id Classroom ID
     */
    public function actionClassDetails($id)
    {
        try {
            // Validate classroom ID format
            $idValidation = ValidationHelper::validateObjectId($id, 'Classroom ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid classroom ID format');
            }

            // Get classroom details using helper
            $detailsResult = ClassroomHelper::getClassroomDetails($id);
            if (!$detailsResult['success']) {
                if (strpos($detailsResult['message'], 'not found') !== false) {
                    throw new CHttpException(404, 'Classroom not found');
                }
                Yii::log('Failed to get classroom details: ' . $detailsResult['message'], 'error', 'TeacherController');
                throw new CHttpException(500, 'Unable to load classroom details');
            }

            $data = $detailsResult['data'];

            // Render the class details view
            $this->render('classDetails', [
                'classId' => $id,
                'classDetails' => $data['classDetails'],
                'students' => $data['students']['students'],
                'performanceData' => $data['performanceData']
            ]);

        } catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in class details: ' . $e->getMessage(), 'error', 'TeacherController');
            throw new CHttpException(500, 'Unable to load class details');
        }
    }

    /**
     * Display attendance taking interface
     */
    public function actionAttendance()
    {
        try {
            // Get the teacher ID from the logged-in user
            $teacherId = Yii::app()->user->getId();
            
            if (!$teacherId) {
                throw new CHttpException(401, 'Teacher not authenticated');
            }

            // Validate teacher ID format
            $idValidation = ValidationHelper::validateObjectId($teacherId, 'Teacher ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid teacher ID format');
            }

            // Get teacher classes using helper
            $classesResult = ClassroomHelper::getTeacherClasses($teacherId);
            if (!$classesResult['success']) {
                Yii::log('Failed to get teacher classes for attendance: ' . $classesResult['message'], 'error', 'TeacherController');
                throw new CHttpException(500, 'Unable to load teacher classes');
            }

            $teacherData = $classesResult['data'];
            $classes = [];
            
            // Format classes for dropdown
            if (isset($teacherData['classes']) && is_array($teacherData['classes'])) {
                foreach ($teacherData['classes'] as $class) {
                    $classes[] = [
                        'id' => (string)$class['_id'],
                        'name' => $class['class_name']
                    ];
                }
            }

            // Get selected class and its students
            $selectedClass = Yii::app()->request->getQuery('class_id');
            $students = [];

            if ($selectedClass) {
                // Validate selected class ID
                $classIdValidation = ValidationHelper::validateObjectId($selectedClass, 'Class ID');
                if ($classIdValidation['success']) {
                    // Get students for the selected class
                    $studentsResult = ClassroomHelper::getStudentsForAttendance($selectedClass);
                    if ($studentsResult['success']) {
                        $students = $studentsResult['data'];
                    } else {
                        Yii::log('Failed to get students for attendance: ' . $studentsResult['message'], 'warning', 'TeacherController');
                    }
                }
            }

            // Render the attendance view
            $this->render('attendance', array(
                'classes' => $classes,
                'students' => $students,
                'selectedClass' => $selectedClass,
            ));

        } catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in attendance view: ' . $e->getMessage(), 'error', 'TeacherController');
            throw new CHttpException(500, 'Unable to load attendance interface');
        }
    }

    /**
     * Save attendance data for a class
     */
    public function actionSaveAttendance()
    {
        try {
            // Ensure this is a POST request
            if (!Yii::app()->request->isPostRequest) {
                throw new CHttpException(400, 'Invalid request method');
            }

            // Get and validate input parameters
            $classId = $_POST['class_id'] ?? '';
            $attendanceDate = $_POST['attendance_date'] ?? '';
            $attendanceDataJson = $_POST['attendance_data'] ?? '';

            // Validate class ID
            $classIdValidation = ValidationHelper::validateObjectId($classId, 'Class ID');
            if (!$classIdValidation['success']) {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'Invalid class ID format'
                ]);
                return;
            }

            // Validate attendance date
            $dateValidation = ValidationHelper::validateDate($attendanceDate, 'Y-m-d', false);
            if (!$dateValidation['success']) {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'Invalid attendance date'
                ]);
                return;
            }

            // Parse and validate attendance data
            $attendanceData = json_decode($attendanceDataJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'Invalid attendance data format'
                ]);
                return;
            }

            $attendanceValidation = ValidationHelper::validateAttendanceData($attendanceData);
            if (!$attendanceValidation['success']) {
                echo CJSON::encode([
                    'success' => false,
                    'message' => 'Attendance data validation failed',
                    'errors' => $attendanceValidation['errors']
                ]);
                return;
            }

            // Save attendance using helper
            $saveResult = AttendanceHelper::saveClassAttendance($classId, $attendanceDate, $attendanceData);
            
            // Return JSON response
            echo CJSON::encode([
                'success' => $saveResult['success'],
                'message' => $saveResult['message']
            ]);

        } catch (CHttpException $e) {
            echo CJSON::encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            Yii::log('Error saving attendance: ' . $e->getMessage(), 'error', 'TeacherController');
            echo CJSON::encode([
                'success' => false,
                'message' => 'An unexpected error occurred while saving attendance'
            ]);
        }
    }

    /**
     * Teacher login action
     */
    public function actionLogin()
    {
        try {
            // Check if the user is already logged in
            if (!Yii::app()->user->isGuest) {
                $this->redirect(array('teacher/index'));
                return;
            }

            // Instantiate a new login form model
            $model = new LoginForm();

            // Check if it is a POST request
            if (isset($_POST['LoginForm'])) {
                $model->attributes = $_POST['LoginForm'];

                // Validate and authenticate using helper
                $authResult = AuthenticationHelper::authenticateUser($model->username, $model->password);
                
                if ($authResult['success']) {
                    // Check if user is a teacher
                    $userData = $authResult['data']['user'];
                    if ($userData['user_type'] !== 'teacher') {
                        Yii::app()->user->setFlash('error', 'Access denied. Teachers only.');
                    } else {
                        // Redirect to teacher dashboard
                        $this->redirect($authResult['data']['redirect_url']);
                        return;
                    }
                } else {
                    // Display error message
                    Yii::app()->user->setFlash('error', $authResult['message']);
                }
            }

            // Render the login view
            $this->render('login', array('model' => $model));

        } catch (Exception $e) {
            Yii::log('Error in teacher login: ' . $e->getMessage(), 'error', 'TeacherController');
            Yii::app()->user->setFlash('error', 'An error occurred during login. Please try again.');
            $this->render('login', array('model' => new LoginForm()));
        }
    }
}