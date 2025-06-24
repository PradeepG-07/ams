<?php
//load by id, create user

class UserHelper
{
    public static function loadUserById($id)
    {
        try {
            Yii::log("Loading user model with id: $id", CLogger::LEVEL_TRACE, 'application.helpers.userHelper');
            $model = User::model()->findByPk($id);
            if ($model === null) {
                Yii::log("User model not found with id: $id", CLogger::LEVEL_WARNING, 'application.helpers.userHelper');
                throw new CHttpException(404, 'The requested user does not exist.');
            }
            return $model;
        } catch (Exception $e) {
            if ($e instanceof CHttpException) {
                Yii::log("HTTP Exception in loadUserById: " . $e->getMessage(), CLogger::LEVEL_WARNING, 'application.helpers.userHelper');
                throw $e; // Re-throw the HTTP exception to be handled by the framework
            }
            Yii::log("Error in loadUserById: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.userHelper');
        }
    }

    public static function deleteUser($id)
    {
        try {
            Yii::log("Deleting user with ID: $id", CLogger::LEVEL_INFO, 'application.helpers.userHelper');
            $model = self::loadUserById($id);
            if ($model->delete()) {
                Yii::log("User with ID: $id deleted successfully", CLogger::LEVEL_INFO, 'application.helpers.userHelper');
                return array(
                    'success' => true,
                    'message' => 'User deleted successfully!'
                );
            } else {
                Yii::log("Failed to delete user with ID: $id", CLogger::LEVEL_WARNING, 'application.helpers.userHelper');
                return array(
                    'success' => false,
                    'message' => 'Failed to delete user.'
                );
            }
        } catch (Exception $e) {
            Yii::log("Error in deleteUser: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.userHelper');
            return array(
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public static function createUser($userData = null)
    {
        Yii::log("Creating new user", CLogger::LEVEL_INFO, 'application.helpers.userHelper');
        $model = new User();
        return self::_update(null, $model, $userData);
    }
    
    public static function updateUser($id, $userData = null)
    {
        Yii::log("Updating user with ID: $id", CLogger::LEVEL_INFO, 'application.helpers.userHelper');
        $model = self::loadUserById($id);
        return self::_update($id, $model, $userData);
    }

    private static function _update($id=null, $model, $userData = null){
        try {
            Yii::log($id==null ? "Creating new user" : "Updating user with ID: $id", CLogger::LEVEL_INFO, 'application.helpers.userHelper');
            $model->attributes = $userData;

            // handle validating embedded documents in before save
            if (!$model->validate() || !$model->save()) {
                Yii::log("Failed to save user: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.userHelper');
                return array(
                    'success' => false,
                    'model' => $model,
                    'message' => 'Failed to save user: ' . json_encode($model->getErrors())
                );
            }
            
            Yii::log("User " . ($id == null ? "created" : "updated") . " successfully with ID: {$id}", CLogger::LEVEL_INFO, 'application.helpers.userHelper');
            
            return array(
                'success' => true,
                'model' => $model,
                'message' => 'User ' . ($id == null ? "created" : "updated") . ' successfully!'
            );
        } catch (Exception $e) {
            Yii::log("Error in ". ($id == null ? "create" : "update") . ": " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.userHelper');
            return array(
                'success' => false,
                'model' => $model,
                'message' => 'An error occurred: ' . $e->getMessage() 
            );
        }
    }
 
}