<?php
use MongoDB\BSON\ObjectId;
 
class TeacherHelper
{

    public static function loadTeacherById($id)
    {
        try {
            Yii::log("Loading teacher model with id: $id", CLogger::LEVEL_TRACE, 'application.helpers.teacherHelper');
 
                $model = Teacher::model()->findByPk($id);
                if ($model === null) {
                    Yii::log("Teacher model not found with id: $id", CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
                    throw new CHttpException(404, 'The requested teacher does not exist.');
                }
                return $model;
            } catch (Exception $e) {
               if($e instanceof CHttpException) {
                    Yii::log("HTTP Exception in loadTeacherById: " . $e->getMessage(), CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
                    throw $e; // Re-throw the HTTP exception to be handled by the framework
                } 
                Yii::log("Error in loadTeacherByUserId: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.teacherHelper');
        }
    }
    

    public static function loadTeacherByUserId($id)
    {
        try {
            Yii::log("Loading teacher model with user_id: $id", CLogger::LEVEL_TRACE, 'application.helpers.teacherHelper');
 
                $criteria = new EMongoCriteria();
                $criteria->addCond('user_id', '==', $id);
                $model = Teacher::model()->find($criteria);
                if ($model === null) {
                    Yii::log("Teacher model not found with user_id: $id", CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
                    throw new CHttpException(404, 'The requested teacher does not exist.');
                }
                return $model;
            } catch (Exception $e) {
                if($e instanceof CHttpException) {
                    Yii::log("HTTP Exception in loadTeacherByUserId: " . $e->getMessage(), CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
                    throw $e; // Re-throw the HTTP exception to be handled by the framework
                } 
                Yii::log("Error in loadTeacherByUserId: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.teacherHelper');
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
 
 