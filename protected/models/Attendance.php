<?php
class Attendance extends EMongoDocument
{
    public $date;
    public $class_id;
    public $student_ids = array();
    public $teacher_id;


    public function getCollectionName()
    {
        return 'attendance';
    }

    public function afterFind(){
        // var_dump(Yii::app()->user->getState('useAfterFindInAttendance'));
        // exit;
        if (!empty($this->teacher_id) && Yii::app()->user->getState('useAfterFindInAttendance') == true) {
            $this->teacher_id = Teacher::model()->findByPk($this->teacher_id);
            $this->teacher_id->user_id = User::model()->findByPk($this->teacher_id->user_id);
        }
        return parent::afterFind();

    }

    public function rules()
    {
        return array(
            ["date,class_id,teacher_id", 'required'],
            // ["date", 'date', 'format' => 'yyyy-MM-dd'],
            ["class_id,student_ids,teacher_id", 'safe'],
        );
    }

    // public function afterFind()
    // {
    //     parent::afterFind();
    //     if (!empty($this->class_id)) {
    //         $this->class_id = Classes::model()->findByPk($this->class_id);
    //     }
    // }

    public function attributeLabels()
    {
        return array(
            'date' => 'Date',
            'class_id' => 'Class',
            'student_ids' => 'Students',
            'teacher_id' => 'Teacher',
        );
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}