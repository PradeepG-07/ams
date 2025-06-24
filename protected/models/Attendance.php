<?php
class Attendance extends EMongoDocument
{
    public $date;
    public $class_id;
    public $students = array();


    public function getCollectionName()
    {
        return 'attendance';
    }

    public function rules()
    {
        return array(
            ["date", 'required'],
            ["date", 'date', 'format' => 'dd-MM-yyyy'],
            ["class_id", 'safe'],
            ["students", 'safe']
        );
    }

    public function attributeLabels()
    {
        return array(
            'date' => 'Date',
            'class_id' => 'Class',
            'students' => 'Students',
        );
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}