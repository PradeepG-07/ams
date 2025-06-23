<?php
class Attendance extends EMongoDocument
{
    public $date;
    public $classes = [];


    public function getCollectionName()
    {
        return 'attendance';
    }

    public function rules()
    {
        return array(
            ["date", 'required'],
            ["date", 'date', 'format' => 'dd-MM-yyyy'],
            ["classes", 'safe']
        );
    }

    public function attributeLabels()
    {
        return array(
            'date' => 'Date',
            'classes' => 'Classes',
        );
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}