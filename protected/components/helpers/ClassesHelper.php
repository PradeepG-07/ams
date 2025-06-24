<?php

class ClassesHelper{
    
    public static function classExists($id){

        try{

            Yii::log("Checking if class exists with id: " . $id, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $class = Classes::model()->findByPk($id);
            if($class){
                return true;
            } else {
                return false;
            }
        }
        catch(Exception $e){
            Yii::log("Error checking class existence: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; // Re-throw the exception for further handling
        }
    }

    public static function loadClassById($id){

        try{

            Yii::log("Loading class model with id: " . $id, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $class = Classes::model()->findByPk($id);
            if($class){
                return $class;
            } else {
                throw new CHttpException(404, 'The requested class does not exist.');
            }
        }
        catch(Exception $e){
            Yii::log("Error loading class by ID: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; // Re-throw the exception for further handling
        }
    }

    public static function createClass($classData = null)
    {

        Yii::log("Creating new class", CLogger::LEVEL_INFO, 'application.helpers.classesHelper');
        
        $model = new Classes;
        return self::_update($model, $classData);
           
    }
    
    public static function updateClass($id, $classData = null)
    {
        Yii::log("Updating a class", CLogger::LEVEL_INFO, 'application.helpers.classesHelper');
        $model = self::loadClassById($id);
        return self::_update($model, $classData);
    }


    private static function _update($model, $classData = null){
        try {
            $id = $model->_id;
            Yii::log($model->_id == null ? "Creating new class" : "Updating class with ID: {$model->_id}", CLogger::LEVEL_INFO, 'application.helpers.classesHelper');
            $model->attributes = $classData;

            // handle validating embedded documents in before save
            if (!$model->validate() || !$model->save()) {
                Yii::log("Failed to save class: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.classesHelper');
                return array(
                    'success' => false,
                    'model' => $model,
                    'message' => 'Failed to save class: ' . json_encode($model->getErrors())
                );
            }
            
            Yii::log("Class " . ($id == null ? "created" : "updated") . " successfully with ID: {$model->_id}", CLogger::LEVEL_INFO, 'application.helpers.classesHelper');
            
            return array(
                'success' => true,
                'model' => $model,
                'message' => 'Class ' . ($id == null ? "created" : "updated") . ' successfully!'
            );
        } catch (Exception $e) {
            Yii::log("Error in ". ($id == null ? "create" : "update") . ": " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            return array(
                'success' => false,
                'model' => $model,
                'message' => 'An error occurred: ' . $e->getMessage() 
            );
        }
    }
    public static function deleteClass($class_id){

        try{

            Yii::log("Deleting class with ID: " . $class_id, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $class = self::loadClassById($class_id);
            //set the class to empty in students
            $criteria = new EMongoCriteria();
            $criteria->class = $class_id;
            $modifier = new EMongoModifier();
            $modifier->addModifier('class','set', "");
            $students = Student::model()->updateAll($modifier, $criteria);

            //remove the id from teachers
            $criteria = new EMongoCriteria();
            $criteria->addCond('classes','in', array($class_id));
            $modifier = new EMongoModifier();
            $modifier->addModifier('classes','pull', $class_id);
            $teachers = Teacher::model()->updateAll($modifier, $criteria);

            $class->delete();
            return true;
            Yii::log("Class with ID: {$class_id} deleted successfully.", CLogger::LEVEL_INFO, 'application.helpers.classesHelper');
        }
        catch(Exception $e){
            Yii::log("Error deleting class: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; 
        }
    }

    public static function listClasses($page = 1, $limit = 10){

        try{

            Yii::log("Listing classes for page: " . $page, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $criteria = new EMongoCriteria();
            $criteria->limit($limit);
            $criteria->offset(($page - 1) * $limit);
            $classes = Classes::model()->findAll($criteria);

            if($classes){
                return $classes;
            } else {
                return [];
            }
        }
        catch(Exception $e){
            Yii::log("Error listing classes: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; // Re-throw the exception for further handling
        }
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
        return Classes::model()->count($criteria);
    }

    public static function getAllClasses(){
        try{
            Yii::log("Fetching all classes", CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');
            $classes = Classes::model()->findAll();
            Yii::log("Fetched " . count($classes) . " classes.", CLogger::LEVEL_INFO, 'application.helpers.classesHelper');
            if($classes){
                return $classes;
            } else {
                return [];
            }
        }
        catch(Exception $e){
            Yii::log("Error fetching all classes: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; // Re-throw the exception for further handling
        }
    }
}