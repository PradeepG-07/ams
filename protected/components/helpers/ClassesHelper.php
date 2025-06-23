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

    public static function createClass($class_name){

        try{

            Yii::log("Creating class with " . $class_name, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $class = new Classes();
            $class->class_name = $class_name;
            // todo: check in before save whether class name exists for uniqueness
            if($class->save()){
                return $class;
            } else {
                if($class->hasErrors()){
                    Yii::log("Error saving class: " . json_encode($class->getErrors()), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
                    throw new CHttpException("Error saving class: " . json_encode($class->getErrors()));
                } else {
                    throw new CHttpException("Unknown error while saving class");
                }
            }
        }
        catch(Exception $e){
            Yii::log("Error creating class: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; // Re-throw the exception for further handling
        }
    }

    public static function updateClass($class_id, $attributes){

        try{

            Yii::log("Updating class with ID: " . $class_id, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $class = Classes::model()->findByPk($class_id);
            if($class){
                $class->attributes = $attributes;
                if($class->save()){
                    return $class;
                } else {
                    if($class->hasErrors()){
                        Yii::log("Error updating class: " . json_encode($class->getErrors()), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
                        throw new CHttpException("Error updating class: " . json_encode($class->getErrors()));
                    } else {
                        throw new CHttpException("Unknown error while updating class");
                    }
                }
            } else {
                throw new CHttpException("Class not found with ID: " . $class_id);
            }
        }
        catch(Exception $e){
            Yii::log("Error updating class: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
            throw $e; 
        }
    }
    public static function deleteClass($class_id){

        try{

            Yii::log("Deleting class with ID: " . $class_id, CLogger::LEVEL_TRACE, 'application.helpers.classesHelper');

            $class = Classes::model()->findByPk($class_id);
            if($class){
                if($class->delete()){
                    return true;
                } else {
                    if($class->hasErrors()){
                        Yii::log("Error deleting class: " . json_encode($class->getErrors()), CLogger::LEVEL_ERROR, 'application.helpers.classesHelper');
                        throw new CHttpException("Error deleting class: " . json_encode($class->getErrors()));
                    } else {
                        throw new CHttpException("Unknown error while deleting class");
                    }
                }
            } else {
                throw new CHttpException("Class not found with ID: " . $class_id);
            }
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
            $criteria->limit($limit)->skip(($page - 1) * $limit);
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

}