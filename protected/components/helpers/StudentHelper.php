<?php
use MongoDB\BSON\ObjectID;
class StudentHelper{
    /**
     * Get the student model by ID
     * @param string $id
     * @return Student|null
     */
    public static function getStudentById($id)
    {
        if (empty($id)) {
            return null;
        }
        try{
            $student = Student::model()->findByPk(new ObjectID($id));
            return $student ? $student : null;
        }
        catch (Exception $e) {
            Yii::log("Error fetching student by ID: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            return null;
        }
    }
}
