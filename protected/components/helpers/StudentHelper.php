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
            $model->class = new ObjectId($model->class); // Ensure class is an ObjectId

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
            $ret = Student::model()->startAggregation()
                ->addStage([
                    '$match' => [
                        'class' =>$classId // Ensure class is an ObjectId
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
            if ($students) {
                Yii::log("Students fetched successfully for student ID: $studentId", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
                return $students[0];
            } else {
                Yii::log("No students found for student ID: $studentId", CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return [];
            }
        } catch (Exception $e) {
            Yii::log("Error fetching students for student ID: $studentId - " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.studentHelper');
            throw new CHttpException(500, 'An error occurred while fetching students: ' . $e->getMessage());
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
}
