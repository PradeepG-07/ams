<?php

use MongoDB\BSON\ObjectId;

class StudentHelper
{

    public static function loadStudentById($id)
    {
        try {
            Yii::log("Loading student model with id: $id", CLogger::LEVEL_TRACE, 'application.helpers.studentHelper');

            $model = Student::model()->findByPk($id);
            if ($model === null) {
                Yii::log("Student model not found with id: $id", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                throw new CHttpException(404, 'The requested student does not exist.');
            }
            return $model;
        } catch (Exception $e) {
            if ($e instanceof CHttpException) {
                Yii::log("HTTP Exception in loadStudentById: " . $e->getMessage(), CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                throw $e; // Re-throw the HTTP exception to be handled by the framework
            }
            Yii::log("Error in loadStudentById: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
        }
    }


    public static function loadStudentByUserId($id)
    {
        try {
            Yii::log("Loading student model with user_id: $id", CLogger::LEVEL_TRACE, 'application.helpers.studentHelper');

            $criteria = new EMongoCriteria();
            $criteria->addCond('user_id', '==', $id);
            $model = Student::model()->find($criteria);
            if ($model === null) {
                Yii::log("Student model not found with user_id: $id", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                throw new CHttpException(404, 'The requested student does not exist.');
            }
            return $model;
        } catch (Exception $e) {
            if ($e instanceof CHttpException) {
                Yii::log("HTTP Exception in loadStudentByUserId: " . $e->getMessage(), CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                throw $e; // Re-throw the HTTP exception to be handled by the framework
            }
            Yii::log("Error in loadStudentByUserId: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
        }
    }

    public static function createStudent($studentData = null, $userId = null)
    {

        Yii::log("Creating new student", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');

        $model = new Student;
        $model->user_id = $userId;
        return self::_update(null, $model, $studentData);
    }

    public static function updateStudent($id, $studentData = null)
    {
        Yii::log("Updating a student", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
        $model = self::loadStudentById($id);
        return self::_update($id, $model, $studentData);
    }


    private static function _update($id = null, $model, $studentData = null)
    {
        try {
            Yii::log($id == null ? "Creating new student" : "Updating student with ID: $id", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            $s3Key = $model->profile_picture_key;
            $model->attributes = $studentData;
            if(!empty($model->class)){
                $model->class = new ObjectId($model->class);
            }
            $uploadedFile = CUploadedFile::getInstance($model, 'profile_picture');
            if ($uploadedFile) {
                Yii::log("Processing profile picture upload", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                // Validate the file type
                $allowedExtensions = array('jpg', 'jpeg', 'png');
                if (!in_array($uploadedFile->getExtensionName(), $allowedExtensions)) {
                    Yii::log("Invalid file type for profile picture upload: " . $uploadedFile->getExtensionName(), CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                    Yii::app()->user->setFlash('error', 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
                    return array(
                        'success' => false,
                        'model' => $model,
                        'message' => 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.'
                    );
                }
                // Delete old profile picture file from S3 if it exists
                if (isset($s3Key) && !empty($s3Key)) {
                    Yii::log("Deleting old profile picture file from S3: $s3Key", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                    S3Helper::deleteObject($s3Key, $_ENV['S3_BUCKET_NAME']);
                }
                $s3Key = 'profile_pictures/profile_picture_' . $model->_id . '.' . $uploadedFile->getExtensionName();
                $stream = file_get_contents($uploadedFile->tempName);
                $result = S3Helper::uploadObject($s3Key, $stream, $_ENV['S3_BUCKET_NAME']);
                if ($result) {
                    // Update the model with the S3 path
                    $model->profile_picture_key = $s3Key;
                } else {
                    Yii::log("Failed to upload profile picture file to S3", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    Yii::app()->user->setFlash('error', 'Failed to upload profile picture file.');
                    return array(
                        'success' => false,
                        'model' => $model,
                        'message' => 'Failed to upload profile picture file.'
                    );
                }
            }
            if (!$model->validate() || !$model->save()) {
                Yii::log("Failed to save student: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return array(
                    'success' => false,
                    'model' => $model,
                    'message' => 'Failed to save student: ' . json_encode($model->getErrors())
                );
            }

            // handle validating embedded documents in before save
            // if (!$model->validate() || !$model->save()) {
            //     Yii::log("Failed to save student: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
            //     return array(
            //         'success' => false,
            //         'model' => $model,
            //         'message' => 'Failed to save student: ' . json_encode($model->getErrors())
            //     );
            // }

            Yii::log("Student " . ($id == null ? "created" : "updated") . " successfully with ID: {$model->_id}", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');

            return array(
                'success' => true,
                'model' => $model,
                'message' => 'Student ' . ($id == null ? "created" : "updated") . ' successfully!'
            );
        } catch (Exception $e) {
            Yii::log("Error in " . ($id == null ? "create" : "update") . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.userHelper');
            return array(
                'success' => false,
                'model' => $model,
                'message' => 'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public static function listStudents($page = 1)
    {
        Yii::log("Listing students for page: $page", CLogger::LEVEL_TRACE, 'application.helpers.studentHelper');

        $aggregationResult = Student::model()->startAggregation()
            ->addStage([
                '$lookup' => [
                    'from' => 'users',
                    'localField' => 'user_id',
                    'foreignField' => '_id',
                    'as' => 'user'
                ]
            ])
            ->addStage([
                '$lookup' => [
                    'from' => 'classes',
                    'localField' => 'class',
                    'foreignField' => '_id',
                    'as' => 'class_info'
                ]
            ])
            ->addStage([
                '$unwind' => [
                    'path' => '$user',
                    'preserveNullAndEmptyArrays' => true
                ]
            ])
            ->addStage([
                '$unwind' => [
                    'path' => '$class_info',
                    'preserveNullAndEmptyArrays' => true
                ]
            ])
            ->sort(['created_at' => EMongoCriteria::SORT_DESC])
            ->skip(($page - 1) * 5)
            ->limit(5)
            ->aggregate();

        $students = $aggregationResult['result'] ?? [];
        //  echo "<pre>";
        // print_r($students);
        // exit;
        $studentsWithUrls = array_map(function($student) {
            // $student is an array from aggregation result
    
            // Convert to Student model instance if needed OR just manually add URL:
            $profileUrl = null;
            if (!empty($student['profile_picture_key'])) {
                $profileUrl = S3Helper::generateGETObjectUrl($student['profile_picture_key']);
            }
    
            $student['profile_picture_url'] = $profileUrl;
    
            return $student;
        }, $students);

        return $studentsWithUrls;
    }


    // public static function findAll($criteria = null)
    // {
    //     Yii::log("Finding all jobs with criteria: " . json_encode($criteria), CLogger::LEVEL_TRACE, 'application.helpers.studentHelper');
    //     if ($criteria === null) {
    //         $criteria = new EMongoCriteria();
    //     }
    //     return Job::model()->findAll($criteria);
    // }

    public static function count($conditions = array())
    {
        Yii::log("Counting jobs with conditions: " . json_encode($conditions), CLogger::LEVEL_TRACE, 'application.helpers.studentHelper');
        if (!is_array($conditions)) {
            Yii::log("Invalid conditions format, expected array.", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new InvalidArgumentException('Conditions must be an array.');
        }
        $criteria = new EMongoCriteria();
        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                $criteria->addCond($condition[0], $condition[1], $condition[2]);
            }
        }
        return Student::model()->count($criteria);
    }

    public static function deleteStudentByUserId($userId)
    {
        Yii::log("Deleting student by user ID: $userId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
        try {
            $student = self::loadStudentByUserId($userId);
            if ($student->delete()) {
                Yii::log("Student with user ID: $userId deleted successfully", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                return array(
                    'success' => true,
                    'message' => 'Student deleted successfully!'
                );
            } else {
                Yii::log("Failed to delete student with user ID: $userId", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return array(
                    'success' => false,
                    'message' => 'Failed to delete student.'
                );
            }
        } catch (Exception $e) {
            Yii::log("Error in deleteStudentByUserId: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            return array(
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public static function getStudentsFromClassName($classId)
    {
        Yii::log("Fetching students for class ID: $classId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
        try {
            // Handle empty or null class ID
            if (empty($classId)) {
                Yii::log("Empty class ID provided, returning empty array", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                return [];
            }
            
            $ret = Student::model()->startAggregation()
                ->addStage([
                    '$match' => [
                        'class' => $classId // Ensure class is an ObjectId
                    ]
                ])
                ->addStage([
                    '$lookup' => [
                        'from' => 'users',
                        'localField' => 'user_id',
                        'foreignField' => '_id',
                        'as' => 'user',
                        'pipeline' => [
                            ['$project' => [
                                'name' => 1
                            ]]
                        ]
                    ]
                ])
                ->addStage([
                    '$unwind' => [
                        'path' => '$user',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ])
                ->aggregate();
            $students = $ret['result'] ?? [];
            if ($students) {
                Yii::log("Students fetched successfully for class ID: $classId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                return $students;
            } else {
                Yii::log("No students found for class ID: $classId", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return [];
            }
        } catch (Exception $e) {
            Yii::log("Error fetching students for class ID: $classId - " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new CHttpException(500, 'An error occurred while fetching students: ' . $e->getMessage());
        }
    }


    public static function getStudentWithClassAndUserPopulated($studentId)
    {
        Yii::log("Fetching student with ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
        try {
            $ret = Student::model()->startAggregation()
                ->addStage([
                    '$match' => [
                        '_id' => $studentId 
                    ]
                ])
                ->addStage([
                    '$lookup' => [
                        'from' => 'classes',
                        'localField' => 'class',
                        'foreignField' => '_id',
                        'as' => 'class_info'
                    ]
                ])
                ->addStage([
                    '$lookup' => [
                        'from' => 'users',
                        'localField' => 'user_id',
                        'foreignField' => '_id',
                        'as' => 'user',
                    ]
                ])
                ->addStage([
                    '$unwind' => [
                        'path' => '$user',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ])
                ->addStage([
                    '$unwind' => [
                        'path' => '$class_info',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ])
                ->aggregate();
            $students = $ret['result'] ?? [];
            if (!empty($students)) {
                Yii::log("Student fetched successfully for student ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                
                // Handle profile picture URL
                if (!empty($students[0]['profile_picture_key'])) {
                    $profileUrl = S3Helper::generateGETObjectUrl($students[0]['profile_picture_key']);
                    $students[0]['profile_picture_url'] = $profileUrl;
                }
                
                // Handle missing class information
                if (empty($students[0]['class_info'])) {
                    $students[0]['class_info'] = [
                        'class_name' => 'No Class Assigned',
                        '_id' => null
                    ];
                    Yii::log("Student ID: $studentId has no class assigned", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                }
                
                return $students[0];
            } else {
                Yii::log("No student found for student ID: $studentId", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                throw new CHttpException(404, 'Student not found.');
            }
        } catch (Exception $e) {
            Yii::log("Error fetching student for student ID: $studentId - " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            if ($e instanceof CHttpException) {
                throw $e;
            }
            throw new CHttpException(500, 'An error occurred while fetching student: ' . $e->getMessage());
        }
    }

    public static function validateStudentIds($student_ids){
        // receives array of student ids
        Yii::log("Validating student IDs: " . json_encode($student_ids), CLogger::LEVEL_TRACE, 'application.helpers.studentHelper');
        if (!is_array($student_ids) || empty($student_ids)) {
            Yii::log("Invalid student IDs format, expected non-empty array.", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new InvalidArgumentException('Student IDs must be a non-empty array.');
        }
        $criteria = new EMongoCriteria();
        $criteria->addCond('_id', 'in', array_map(function($id) {
            return new ObjectId($id);
        }, $student_ids));
        $students = Student::model()->count($criteria);
        if ($students !== count($student_ids)) {
            Yii::log("Some student IDs are invalid.", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
            return false;
        }
        Yii::log("All student IDs are valid.", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
        return true;
    }

    public static function getAttendanceBetweenDates($id, $fromDate, $toDate){
        try{
            Yii::log("Fetching attendance for student ID: $id between dates $fromDate and $toDate", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            if (empty($id) || empty($fromDate) || empty($toDate)) {
                Yii::log("Invalid parameters provided for fetching attendance.", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Invalid parameters provided for fetching attendance.');
            }
            // Ensure dates are in valid format
            $fromDate = date('Y-m-d', strtotime($fromDate));
            $toDate = date('Y-m-d', strtotime($toDate));
            if (!$fromDate || !$toDate) {
                Yii::log("Invalid date format provided for fetching attendance.", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Invalid date format provided for fetching attendance.');
            }
            // Ensure $id is a valid ObjectId
            if (!preg_match('/^[0-9a-f]{24}$/', $id)) {
                Yii::log("Invalid student ID format provided for fetching attendance: $id", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Invalid student ID format provided for fetching attendance.');
            }
            $aggregationResult = Attendance::model()->startAggregation()
            ->addStage([
                '$match' => [
                    'date' => [
                        '$lte' => new MongoDate(strtotime($toDate)),
                        '$gte' => new MongoDate(strtotime($fromDate))
                    ],
                    '$expr' => [
                        '$in' => [new ObjectId($id), '$student_ids']
                    ]
                ]
            ])
            ->aggregate();
            Yii::log("Attendance fetched successfully for student ID: $id between dates $fromDate and $toDate", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            if (empty($aggregationResult['result'])) {
                Yii::log("No attendance records found for student ID: $id between dates $fromDate and $toDate", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return [];
            }
            return $aggregationResult['result'] ?? [];
        }
        catch(Exception $e){
            Yii::log("Error fetching attendance between dates: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new CHttpException(500, 'An error occurred while fetching attendance: ' . $e->getMessage());
        }
    }

    public static function getAttendanceBetweenDatesWithPagination($id, $fromDate, $toDate, $page = 1, $pageSize = 10){
        try{
            Yii::log("Fetching paginated attendance for student ID: $id between dates $fromDate and $toDate (page: $page, pageSize: $pageSize)", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            if (empty($id) || empty($fromDate) || empty($toDate)) {
                Yii::log("Invalid parameters provided for fetching attendance.", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Invalid parameters provided for fetching attendance.');
            }
            
            // Ensure dates are in valid format
            $fromDate = date('Y-m-d', strtotime($fromDate));
            $toDate = date('Y-m-d', strtotime($toDate));
            if (!$fromDate || !$toDate) {
                Yii::log("Invalid date format provided for fetching attendance.", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Invalid date format provided for fetching attendance.');
            }
            
            // Ensure $id is a valid ObjectId
            if (!preg_match('/^[0-9a-f]{24}$/', $id)) {
                Yii::log("Invalid student ID format provided for fetching attendance: $id", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Invalid student ID format provided for fetching attendance.');
            }

            $studentObjectId = new ObjectId($id);
            $skip = ($page - 1) * $pageSize;

            // First get total count
            $countResult = Attendance::model()->startAggregation()
                ->addStage([
                    '$match' => [
                        'date' => [
                            '$lte' => new MongoDate(strtotime($toDate)),
                            '$gte' => new MongoDate(strtotime($fromDate))
                        ],
                        '$expr' => [
                            '$in' => [$studentObjectId, '$student_ids']
                        ]
                    ]
                ])
                ->addStage([
                    '$count' => 'total'
                ])
                ->aggregate();

            $total = isset($countResult['result'][0]['total']) ? $countResult['result'][0]['total'] : 0;

            // Then get paginated data with class information
            $aggregationResult = Attendance::model()->startAggregation()
                ->addStage([
                    '$match' => [
                        'date' => [
                            '$lte' => new MongoDate(strtotime($toDate)),
                            '$gte' => new MongoDate(strtotime($fromDate))
                        ],
                        '$expr' => [
                            '$in' => [$studentObjectId, '$student_ids']
                        ]
                    ]
                ])
                ->addStage([
                    '$lookup' => [
                        'from' => 'teachers',
                        'localField' => 'teacher_id',
                        'foreignField' => '_id',
                        'as' => 'teacher_info',
                        'pipeline' => [
                            [
                                '$lookup' => [
                                    'from' => 'users',
                                    'localField' => 'user_id',
                                    'foreignField' => '_id',
                                    'as' => 'user_info'
                                ]
                            ],
                            [
                                '$unwind' => [
                                    'path' => '$user_info',
                                    'preserveNullAndEmptyArrays' => true
                                ]
                            ],
                            [
                                '$project' => [
                                    'name' => '$user_info.name',
                                    'email' => '$user_info.email',
                                    '_id' => 0
                                ]
                            ]
                        ]
                    ]
                ])
                ->addStage([
                    '$lookup' => [
                        'from' => 'classes',
                        'localField' => 'class_id',
                        'foreignField' => '_id',
                        'as' => 'class_info'
                    ]
                ])
                ->addStage([
                    '$unwind' => [
                        'path' => '$class_info',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ])
                ->addStage([
                    '$addFields' => [
                        'class_name' => '$class_info.name'
                    ]
                ])
                ->addStage([
                    '$unwind' => [
                        'path' => '$teacher_info',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ])
                ->addStage([
                    '$addFields' => [
                        'teacher_name' => '$teacher_info.name'
                    ]
                ])
                ->sort(['date' => EMongoCriteria::SORT_DESC])
                ->skip($skip)
                ->limit($pageSize)
                ->aggregate();

            $data = $aggregationResult['result'] ?? [];
            
            Yii::log("Paginated attendance fetched successfully for student ID: $id between dates $fromDate and $toDate", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            
            return array(
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize
            );
            
        } catch(Exception $e){
            Yii::log("Error fetching paginated attendance between dates: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new CHttpException(500, 'An error occurred while fetching attendance: ' . $e->getMessage());
        }
    }

    public static function getAttendanceDataProvider($studentId, $fromDate, $toDate, $pageSize = 5)
    {
        try {
            Yii::log("Getting attendance data provider for student ID: $studentId, fromDate: $fromDate, toDate: $toDate", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            
            // Validate input parameters
            if (empty($studentId)) {
                Yii::log("Student ID is required for attendance data provider", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                throw new InvalidArgumentException('Student ID is required');
            }
            
            // Ensure studentId is ObjectId
            if (!($studentId instanceof ObjectId)) {
                if (!preg_match('/^[0-9a-f]{24}$/', $studentId)) {
                    Yii::log("Invalid student ID format: $studentId", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    throw new InvalidArgumentException('Invalid student ID format');
                }
                $studentId = new ObjectId($studentId);
            }
            
            $attendanceData = [];
            
            // Only process if both dates are provided
            if (!empty($fromDate) && !empty($toDate)) {
                Yii::log("Creating criteria for date range: $fromDate to $toDate", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                
                // Validate date formats
                $fromTimestamp = strtotime($fromDate);
                $toTimestamp = strtotime($toDate);
                
                if ($fromTimestamp === false || $toTimestamp === false) {
                    Yii::log("Invalid date format provided - fromDate: $fromDate, toDate: $toDate", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    throw new InvalidArgumentException('Invalid date format provided');
                }
                
                if ($fromTimestamp > $toTimestamp) {
                    Yii::log("From date ($fromDate) cannot be later than to date ($toDate)", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    throw new InvalidArgumentException('From date cannot be later than to date');
                }
                // check if both are less than today
                $today = strtotime(date('Y-m-d'));
                if ($fromTimestamp > $today || $toTimestamp > $today) {
                    Yii::log("Date range cannot be in the future - fromDate: $fromDate, toDate: $toDate", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    throw new InvalidArgumentException('Date range cannot be in the future');
                }
                $classId = Yii::app()->user->getState('studentClassId');
                if (empty($classId)) {
                    Yii::log("Class ID is required for attendance data provider", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    throw new InvalidArgumentException('Class ID is required');
                }
                Yii::log("Creating EMongoCriteria for attendance data provider", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');

                $criteria = new EMongoCriteria();
                // $criteria->addCond('student_ids', 'in', array($studentId));
                $criteria->addCond('date', '>=', new MongoDate($fromTimestamp));
                $criteria->addCond('date', '<=', new MongoDate($toTimestamp));
                $criteria->addCond('class_id', '==', new ObjectId($classId));

                $criteria->sort('date', EMongoCriteria::SORT_DESC);
                Yii::log("Creating EMongoDocumentDataProvider with criteria", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                
                $attendanceData = new EMongoDocumentDataProvider('Attendance', array(
                    'criteria' => $criteria,
                    'pagination' => array(
                        'pageSize' => $pageSize,    
                    )
                ));
                // echo "<pre>";
                // print_r($attendanceData->getData());
                // exit;
                Yii::log("Attendance data provider created successfully", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            } else {
                Yii::log("Empty date range provided, returning empty data provider", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            }
            
            return $attendanceData;
            
        } catch (Exception $e) {
            Yii::log("Error creating attendance data provider: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw $e;
        }
    }

    public static function calculateAttendance($studentId){
        /*
        student has class id.
        can use class id in 
        can join from students to attendance
        match the class id to be the class in student
        and check for the student _id prescence 
        in all of the class records
        from there we can get total sessions
        and sessions attended
        */

        try{
            // First check if student has a class assigned
            $student = Student::model()->findByPk($studentId);
            if (!$student || empty($student->class)) {
                Yii::log("Student ID: $studentId has no class assigned, returning zero attendance", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                return [
                    'total_sessions' => 0,
                    'sessions_attended' => 0,
                    'attendance_percentage' => 0
                ];
            }
            
            $aggregationResult = Student::model()->startAggregation()
            ->addStage([
                '$match' => [
                    '_id' => $studentId
                ]
            ])
            ->addStage([
                '$lookup' => [
                    'from' => 'attendance',
                    'localField' => 'class',
                    'foreignField' => 'class_id',
                    'as' => 'result'
                ]
            ])
            ->addStage([
                '$addFields' => [
                    'total_sessions' => [
                        '$size' => '$result'
                    ],
                    'sessions_attended' => [
                        '$size' => [
                            '$filter' => [
                                'input' => '$result',
                                'as' => 'attendance',
                                'cond' => [
                                    '$in' => [$studentId, '$$attendance.student_ids']
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->addStage([
                '$project' => [
                    'total_sessions' => 1,
                    'sessions_attended' => 1,
                    'attendance_percentage' => [
                        '$cond' => [
                            'if' => ['$eq' => ['$total_sessions', 0]],
                            'then' => 0,
                            'else' => [
                                '$multiply' => [
                                    ['$divide' => ['$sessions_attended', '$total_sessions']],
                                    100
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->aggregate();
            if (empty($aggregationResult['result'])) {
                Yii::log("No attendance records found for student ID: $studentId", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return [
                    'total_sessions' => 0,
                    'sessions_attended' => 0,
                    'attendance_percentage' => 0
                ];
            }
            Yii::log("Attendance calculated for student ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            return $aggregationResult['result'][0];
        }
        catch(Exception $e){
            Yii::log("Error calculating attendance for student ID: $studentId - " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new CHttpException(500, 'An error occurred while calculating attendance: ' . $e->getMessage());
        }
        
    }

    public static function deleteProfilePicture($studentId)
    {
        try {
            Yii::log("Deleting profile picture for student ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            if (!$studentId) {
                Yii::log("Missing required parameters for profile picture deletion", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return false;
            }
            // Update student record to remove profile_picture_key
            $studentObjectId = new ObjectId($studentId);
            $student = self::loadStudentById($studentObjectId);

            if ($student) {
                $bucketName = $_ENV['S3_BUCKET_NAME'];
                $deleteResult = S3Helper::deleteObject($student->profile_picture_key, $bucketName);
                $student->profile_picture_key = null;
                if (!$student->save()) {
                    Yii::log("Failed to update student record after deleting profile picture", CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
                    return false;
                }
                Yii::log("Profile picture deleted successfully for student ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                return true;
            } else {
                Yii::log("Student not found for profile picture deletion", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return false;
            }

            Yii::log("Profile picture deleted successfully for student ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            return true;

        } catch (Exception $e) {
            Yii::log("Error deleting profile picture: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            return false;
        }
    }
}
