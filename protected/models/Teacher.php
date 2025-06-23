<?php
 
class Teacher extends EMongoDocument
{
    public $emp_id;
    public $user_id;
    public $salary;
    public $designation;
    public $classes = [];
 
    public function getCollectionName()
    {
        return 'teachers';
    }
 
    public function rules()
    {
        return array(
            ['emp_id,user_id', 'required'],
            ['salary', 'numerical'],
            ['designation', 'length', 'max' => 255],
            ['emp_id, user_id, salary, designation, classes', 'safe']
        );
    }
 
    public function attributeLabels()
    {
        return [
            'emp_id' => 'Employee ID',
            'user_id' => 'User ID',
            'salary' => 'Salary',
            'designation' => 'Designation',
            'classes' => 'Classes'
        ];
    }
 
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
