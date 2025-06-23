<?php
 
class Classes extends EMongoDocument
{
    public $class_name;


    public function getCollectionName()
    {
        return 'classes'; 
    }

    public function rules()
    {
        return [
            ['class_name', 'safe']
        ];
    }
 
    public function attributeLabels()
    {
        return [
            'class_name' => 'Class Name',
        ];
    }
 
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}