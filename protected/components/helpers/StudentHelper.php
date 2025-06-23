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
                if($e instanceof CHttpException) {
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
                if($e instanceof CHttpException) {
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


    private static function _update($id=null, $model, $studentData = null){
        try {
            Yii::log($id==null ? "Creating new student" : "Updating student with ID: $id", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            $model->attributes = $studentData;

            // handle validating embedded documents in before save
            if (!$model->validate() || !$model->save()) {
                Yii::log("Failed to save student: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.studentHelper');
                return array(
                    'success' => false,
                    'model' => $model,
                    'message' => 'Failed to save student: ' . json_encode($model->getErrors())
                );
            }
            
            Yii::log("Student " . ($id == null ? "created" : "updated") . " successfully with ID: {$model->_id}", CLogger::LEVEL_INFO, 'application.helpers.studentHelper');
            
            return array(
                'success' => true,
                'model' => $model,
                'message' => 'Student ' . ($id == null ? "created" : "updated") . ' successfully!'
            );
        } catch (Exception $e) {
            Yii::log("Error in ". ($id == null ? "create" : "update") . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.userHelper');
            return array(
                'success' => false,
                'model' => $model,
                'message' => 'An error occurred: ' . $e->getMessage() 
            );
        }
    }
 
   
    // public static function findAll($criteria = null)
    // {
    //     Yii::log("Finding all jobs with criteria: " . json_encode($criteria), CLogger::LEVEL_TRACE, 'application.helpers.jobHelper');
    //     if ($criteria === null) {
    //         $criteria = new EMongoCriteria();
    //     }
    //     return Job::model()->findAll($criteria);
    // }
 
    // public static function count($conditions = array())
    // {
    //     Yii::log("Counting jobs with conditions: " . json_encode($conditions), CLogger::LEVEL_TRACE, 'application.helpers.jobHelper');
    //     if (!is_array($conditions)) {
    //         Yii::log("Invalid conditions format, expected array.", CLogger::LEVEL_ERROR, 'application.helpers.jobHelper');
    //         throw new InvalidArgumentException('Conditions must be an array.');
    //     }
    //     $criteria = new EMongoCriteria();
 
 
    //     if (!empty($conditions)) {
    //         foreach ($conditions as $condition) {
    //             $criteria->addCond($condition[0], $condition[1], $condition[2]);
    //         }
    //     }
    //     return Job::model()->count($criteria);
    // }
}
 