<?php
class Attendance extends EMongoDocument
{
    public $date;
    public $class_id;
    public $student_ids = array();


    public function getCollectionName()
    {
        return 'attendance';
    }

    public function rules()
    {
        return array(
            ["date", 'required'],
            ["date", 'date', 'format' => 'yyyy-MM-dd'],
            ["class_id", 'safe'],
            ["student_ids", 'safe']
        );
    }

    public function attributeLabels()
    {
        return array(
            'date' => 'Date',
            'class_id' => 'Class',
            'student_ids' => 'Students',
        );
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}