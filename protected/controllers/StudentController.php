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
                'actions' => array('dashboard', 'stats', 'daywise', 'profile', 'getProfile', 'uploadProfilePicture', 'removeProfilePicture'),
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

    /**
     * Display student dashboard with attendance data and analytics
     */
    public function actionDashboard()
    {
        try {
            // Get the logged-in student's ID
            $studentId = Yii::app()->user->id;
            
            if (!$studentId) {
                $this->redirect(array('/site/login'));
                return;
            }

            // Validate student ID format
            $idValidation = ValidationHelper::validateObjectId($studentId, 'Student ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid student ID format');
            }

            // Load the student from database
            $student = Student::model()->findByPk(new ObjectId($studentId));
            if (!$student) {
                throw new CHttpException(404, 'Student not found');
            }

            // Calculate attendance data using helper
            $attendanceResult = AttendanceHelper::calculateStudentAttendanceData($student);
            if (!$attendanceResult['success']) {
                Yii::log('Failed to calculate attendance data: ' . $attendanceResult['message'], 'error', 'StudentController');
                throw new CHttpException(500, 'Unable to load attendance data');
            }

            // Generate recent notifications (placeholder - can be replaced with actual notification system)
            $recentNotifications = $this->generateRecentNotifications();

            // Render the dashboard view
            $this->render('dashboard', [
                'student' => $student,
                'attendanceData' => $attendanceResult['data'],
                'recentNotifications' => $recentNotifications,
            ]);

        } catch (CHttpException $e) {
            // Re-throw HTTP exceptions
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in student dashboard: ' . $e->getMessage(), 'error', 'StudentController');
            throw new CHttpException(500, 'Unable to load dashboard data');
        }
    }

    /**
     * Display day-wise attendance calendar view
     */
    public function actionDaywise()
    {
        try {
            // Get the logged-in student's ID
            $studentId = Yii::app()->user->id;
            
            if (!$studentId) {
                $this->redirect(array('/site/login'));
                return;
            }

            // Validate student ID format
            $idValidation = ValidationHelper::validateObjectId($studentId, 'Student ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid student ID format');
            }

            // Get and validate date parameter
            $date = Yii::app()->request->getParam('date', date('Y-m-d'));
            $dateValidation = ValidationHelper::validateDate($date, 'Y-m-d', true, false);
            if (!$dateValidation['success']) {
                $date = date('Y-m-d'); // Fallback to today
            }

            $selectedTimestamp = strtotime($date);

            // Load the student from database
            $student = Student::model()->findByPk(new ObjectId($studentId));
            if (!$student) {
                throw new CHttpException(404, 'Student not found');
            }

            // Generate calendar data for the current month
            $currentMonth = date('m', $selectedTimestamp);
            $currentYear = date('Y', $selectedTimestamp);
            $firstDayOfMonth = strtotime("$currentYear-$currentMonth-01");
            $daysInMonth = date('t', $firstDayOfMonth);
            $startDayOfWeek = date('N', $firstDayOfMonth) % 7;

            // Get navigation dates
            $prevMonth = date('Y-m-d', strtotime('-1 month', $firstDayOfMonth));
            $nextMonth = date('Y-m-d', strtotime('+1 month', $firstDayOfMonth));

            // Generate calendar data using helper
            $calendarResult = AttendanceHelper::generateCalendarData($student, $currentYear, $currentMonth, $daysInMonth);
            if (!$calendarResult['success']) {
                Yii::log('Failed to generate calendar data: ' . $calendarResult['message'], 'error', 'StudentController');
                throw new CHttpException(500, 'Unable to load attendance data');
            }

            $calendarData = $calendarResult['data'];
            $selectedDateInfo = isset($calendarData[$date]) ? $calendarData[$date] : null;

            // Render the daywise view
            $this->render('daywise', [
                'student' => $student,
                'calendarData' => $calendarData,
                'selectedDate' => $date,
                'selectedDateInfo' => $selectedDateInfo,
                'currentMonth' => date('F Y', $firstDayOfMonth),
                'prevMonth' => $prevMonth,
                'nextMonth' => $nextMonth,
                'startDayOfWeek' => $startDayOfWeek,
                'daysInMonth' => $daysInMonth
            ]);

        } catch (CHttpException $e) {
            // Re-throw HTTP exceptions
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in student daywise view: ' . $e->getMessage(), 'error', 'StudentController');
            throw new CHttpException(500, 'Unable to load attendance data');
        }
    }

    /**
     * Generate recent notifications for the student
     * This is a placeholder method that can be replaced with actual notification logic
     * 
     * @return array Array of notification objects
     */
    private function generateRecentNotifications()
    {
        return [
            [
                'message' => 'Your attendance has been updated.',
                'date' => date('Y-m-d'),
                'read' => false
            ],
            [
                'message' => 'Welcome to the Student Portal!',
                'date' => date('Y-m-d', strtotime('-1 day')),
                'read' => true
            ],
        ];
    }

    /**
     * Display the logged-in student's profile
     */
    public function actionProfile()
    {
        try {
            // Get the logged-in student's ID
            $studentId = Yii::app()->user->id;
            
            if (!$studentId) {
                $this->redirect(array('/site/login'));
                return;
            }

            // Validate student ID format
            $idValidation = ValidationHelper::validateObjectId($studentId, 'Student ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid student ID format');
            }

            $student = StudentHelper::getStudentById($studentId);
            if (!$student) {
                throw new CHttpException(404, 'Student not found');
            }
            
            $this->render('profile', [
                'student' => $student,
            ]);
        } catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in student profile view: ' . $e->getMessage(), 'error', 'StudentController');
            throw new CHttpException(500, 'Unable to load student profile');
        }
    }

    /**
     * Upload profile picture for the logged-in student
     */
    public function actionUploadProfilePicture()
    {
        try {
            // Get the logged-in student's ID
            $studentId = Yii::app()->user->id;
            
            if (!$studentId) {
                $this->redirect(array('/site/login'));
                return;
            }

            // Validate student ID format
            $idValidation = ValidationHelper::validateObjectId($studentId, 'Student ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid student ID format');
            }

            $student = StudentHelper::getStudentById($studentId);
            if (!$student) {
                throw new CHttpException(404, 'Student not found');
            }

            // Handle file upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_picture'];
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png'];
                if (!in_array($file['type'], $allowedTypes)) {
                    Yii::app()->user->setFlash('error', 'Please upload a valid image file (JPG, PNG, or GIF).');
                    $this->redirect(array('profile'));
                    return;
                }
                
                // Validate file size (max 5MB)
                if ($file['size'] > 5 * 1024 * 1024) {
                    Yii::app()->user->setFlash('error', 'File size must be less than 5MB.');
                    $this->redirect(array('profile'));
                    return;
                }
                
                // If changing existing picture, delete the old one from S3
                if (!empty($student->profile_picture)) {
                    // The profile_picture field now contains only the S3 key
                    try {
                        S3Helper::deleteObject($student->profile_picture, $_ENV['S3_BUCKET_NAME']);
                    } catch (Exception $e) {
                        Yii::log('Failed to delete old profile picture from S3: ' . $e->getMessage(), 'warning', 'StudentController');
                    }
                }
                
                // Generate unique S3 key
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $s3Key = 'profile_pictures/' . $studentId . '/profile_' . time() . '.' . $fileExtension;
                
                // Read file content
                $fileContent = file_get_contents($file['tmp_name']);
                
                // Upload to S3
                $uploadResult = S3Helper::uploadObject($s3Key, $fileContent, $_ENV['S3_BUCKET_NAME']);
                
                if ($uploadResult['success']) {
                    // Store only the S3 key in the database
                    $student->profile_picture = $s3Key;
                    if ($student->save()) {
                        Yii::app()->user->setFlash('success', 'Profile picture updated successfully.');
                    } else {
                        Yii::app()->user->setFlash('error', 'Failed to save profile picture information.');
                        // Clean up uploaded file from S3 if database save failed
                        try {
                            S3Helper::deleteObject($s3Key, $_ENV['S3_BUCKET_NAME']);
                        } catch (Exception $e) {
                            Yii::log('Failed to cleanup S3 file after database save failure: ' . $e->getMessage(), 'error', 'StudentController');
                        }
                    }
                } else {
                    Yii::app()->user->setFlash('error', 'Failed to upload file to cloud storage: ' . $uploadResult['message']);
                }
            } else {
                Yii::app()->user->setFlash('error', 'No file uploaded or upload error occurred.');
            }

            $this->redirect(array('profile'));
        } catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in profile picture upload: ' . $e->getMessage(), 'error', 'StudentController');
            throw new CHttpException(500, 'Unable to upload profile picture');
        }
    }

    /**
     * Remove profile picture for the logged-in student
     */
    public function actionRemoveProfilePicture()
    {
        try {
            // Get the logged-in student's ID
            $studentId = Yii::app()->user->id;
            
            if (!$studentId) {
                $this->redirect(array('/site/login'));
                return;
            }

            // Validate student ID format
            $idValidation = ValidationHelper::validateObjectId($studentId, 'Student ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid student ID format');
            }

            $student = StudentHelper::getStudentById($studentId);
            if (!$student) {
                throw new CHttpException(404, 'Student not found');
            }

            // Check if student has a profile picture
            if (empty($student->profile_picture)) {
                Yii::app()->user->setFlash('error', 'No profile picture to remove.');
                $this->redirect(array('profile'));
                return;
            }

            // The profile_picture field now contains only the S3 key
            try {
                // Delete the file from S3 using the stored S3 key
                S3Helper::deleteObject($student->profile_picture, $_ENV['S3_BUCKET_NAME']);
            } catch (Exception $e) {
                Yii::log('Failed to delete profile picture from S3: ' . $e->getMessage(), 'warning', 'StudentController');
                // Continue with database update even if S3 deletion fails
            }

            // Remove the profile picture reference from database
            $student->profile_picture = null;
            if ($student->save()) {
                Yii::app()->user->setFlash('success', 'Profile picture removed successfully.');
            } else {
                Yii::app()->user->setFlash('error', 'Failed to remove profile picture from database.');
            }

            $this->redirect(array('profile'));
        } catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in profile picture removal: ' . $e->getMessage(), 'error', 'StudentController');
            throw new CHttpException(500, 'Unable to remove profile picture');
        }
    }

    /**
     * Extract S3 key from S3 URL
     * @param string $url The S3 URL
     * @return string|null The S3 key or null if not a valid S3 URL
     */
    private function extractS3KeyFromUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // Handle direct S3 URLs like: https://bucket.s3.region.amazonaws.com/key
        if (preg_match('/https:\/\/[^\/]+\.s3\.[^\/]+\.amazonaws\.com\/(.+)/', $url, $matches)) {
            return $matches[1];
        }

        // Handle S3 URLs with path-style access: https://s3.region.amazonaws.com/bucket/key
        if (preg_match('/https:\/\/s3\.[^\/]+\.amazonaws\.com\/[^\/]+\/(.+)/', $url, $matches)) {
            return $matches[1];
        }

        // If it's already a key (starts with profile_pictures/)
        if (strpos($url, 'profile_pictures/') === 0) {
            return $url;
        }

        return null;
    }

    public function actionGetProfile($id){
        try{
            // Validate student ID format
            $idValidation = ValidationHelper::validateObjectId($id, 'Student ID');
            if (!$idValidation['success']) {
                throw new CHttpException(400, 'Invalid student ID format');
            }

            $student = StudentHelper::getStudentById($id);
            if (!$student) {
                throw new CHttpException(404, 'Student not found');
            }
            $this->render('profile', [
                'student' => $student,
            ]);
        }
        catch (CHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Yii::log('Error in student profile view: ' . $e->getMessage(), 'error', 'StudentController');
            throw new CHttpException(500, 'Unable to load student profile');
        }
    }
}