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
            ['class_name', 'safe'],
            ['class_name', 'required'],
            ['class_name', 'checkUniqueClassName'],
            ['class_name', 'length', 'max' => 255],
        ];
    }
 
    public function attributeLabels()
    {
        return [
            'class_name' => 'Class Name',
        ];
    }
 
    public function checkUniqueClassName($attribute, $params)
    {
        $criteria = new EMongoCriteria();
        $criteria->addCond('class_name', '==', $this->class_name);
        $existingClass = Classes::model()->find($criteria);
        if ($existingClass && $existingClass->_id != $this->_id) {
            $this->addError($attribute, 'This class name already exists.'); 
        }
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}