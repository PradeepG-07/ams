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
            if ($e instanceof CHttpException) {
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
            if ($e instanceof CHttpException) {
                Yii::log("HTTP Exception in loadTeacherByUserId: " . $e->getMessage(), CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
                throw $e; // Re-throw the HTTP exception to be handled by the framework
            }
            Yii::log("Error in loadTeacherByUserId: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.teacherHelper');
        }
    }

    public static function createTeacher($teacherData = null, $userId = null)
    {

        Yii::log("Creating new teacher", CLogger::LEVEL_INFO, 'application.helpers.teacherHelper');

        $model = new Teacher();
        $model->user_id = $userId;
        return self::_update(null, $model, $teacherData);
    }

    public static function updateTeacher($id, $teacherData = null)
    {
        Yii::log("Updating a teacher", CLogger::LEVEL_INFO, 'application.helpers.teacherHelper');
        $model = self::loadTeacherById($id);
        return self::_update($id, $model, $teacherData);
    }


    private static function _update($id = null, $model, $teacherData = null)
    {
        try {
            Yii::log($id == null ? "Creating new teacher" : "Updating teacher with ID: $id", CLogger::LEVEL_INFO, 'application.helpers.teacherHelper');
            $model->attributes = $teacherData;
            foreach ($model->classes as $key => $class) {
                if (is_string($class)) {
                    $model->classes[$key] = new ObjectId($class);
                } elseif (is_array($class) && isset($class['_id']) && is_string($class['_id'])) {
                    $model->classes[$key] = new ObjectId($class['_id']);
                }
            }
            // handle validating embedded documents in before save
            if (!$model->validate() || !$model->save()) {
                Yii::log("Failed to save teacher: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
                return array(
                    'success' => false,
                    'model' => $model,
                    'message' => 'Failed to save teacher: ' . json_encode($model->getErrors())
                );
            }

            Yii::log("Teacher " . ($id == null ? "created" : "updated") . " successfully with ID: {$model->_id}", CLogger::LEVEL_INFO, 'application.helpers.teacherHelper');

            return array(
                'success' => true,
                'model' => $model,
                'message' => 'Teacher ' . ($id == null ? "created" : "updated") . ' successfully!'
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


    public static function listTeachers($page = 1)
{
    Yii::log("Listing teachers for page: $page", CLogger::LEVEL_TRACE, 'application.helpers.teacherHelper');

    $aggregationResult = Teacher::model()->startAggregation()
        ->addStage([
            '$lookup' => [
                'from' => 'classes',
                'localField' => 'classes',
                'foreignField' => '_id',
                'as' => 'classes'
            ]
        ])
        ->addStage([
            '$lookup' => [
                'from' => 'users',
                'localField' => 'user_id',
                'foreignField' => '_id',
                'as' => 'user'
            ]
        ])
        ->addStage([
            '$unwind' => [
                'path' => '$user',
                'preserveNullAndEmptyArrays' => true
            ]
        ])
        ->addStage([
            '$sort' => ['created_at' => -1]  // EMongoCriteria::SORT_DESC is -1
        ])
        ->addStage([
            '$skip' => ($page - 1) * 5
        ])
        ->addStage([
            '$limit' => 5
        ])
        ->aggregate();

    $teachers = $aggregationResult['result'] ?? [];

    // echo "<pre>";
    // print_r($teachers);
    // exit;
    return $teachers;
}

    public static function getTeacherWithPopulatedClasses($teacherId)
    {
        Yii::log("Getting teacher with populated classes for teacher ID: $teacherId", CLogger::LEVEL_TRACE, 'application.helpers.teacherHelper');

        $result = Teacher::model()->startAggregation()
            ->addStage([
                '$match' => [
                    '_id' => $teacherId
                ]
            ])
            ->addStage([
                '$lookup' => [
                    'from' => 'classes',
                    'localField' => 'classes',
                    'foreignField' => '_id',
                    'as' => 'classes'
                ]
            ])
            // ->addStage([
            //     '$unwind' => [
            //         'path' => '$classes',
            //         'preserveNullAndEmptyArrays' => true
            //     ]
            // ])
            ->aggregate();

        $teacher = $result['result'] ?? null;

        if ($teacher === null) {
            Yii::log("Teacher not found with ID: $teacherId", CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
            throw new CHttpException(404, 'The requested teacher does not exist.');
        }
        return $teacher[0];
    }

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
        return Teacher::model()->count($criteria);
    }

    public static function deleteTeacherByUserId($id)
    {
        Yii::log("Deleting teacher by user ID: $id", CLogger::LEVEL_INFO, 'application.helpers.teacherHelper');
        $model = self::loadTeacherByUserId($id);
        if ($model === null) {
            Yii::log("Teacher not found with user ID: $id", CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
            return array(
                'success' => false,
                'message' => 'Teacher not found.'
            );
        }
        if ($model->delete()) {
            Yii::log("Teacher with user ID: $id deleted successfully", CLogger::LEVEL_INFO, 'application.helpers.teacherHelper');
            return array(
                'success' => true,
                'message' => 'Teacher deleted successfully!'
            );
        } else {
            Yii::log("Failed to delete teacher with user ID: $id", CLogger::LEVEL_WARNING, 'application.helpers.teacherHelper');
            return array(
                'success' => false,
                'message' => 'Failed to delete teacher.'
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
