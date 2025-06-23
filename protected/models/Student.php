<?php
 
class Student extends EMongoDocument
{
    public $roll_no;
    public $user_id;
    public $cgpa;
    public $class;
    public $hobbies = [];
    public $profile_picture;

 
    public function getCollectionName()
    {
        return 'students'; 
    }

    public function rules()
    {
        return array(
            // ['roll_no,user_id,cgpa,class,hobbies', 'required'],
            ['roll_no,user_id,cgpa,class,hobbies', 'safe'],
            ['roll_no', 'length', 'max' => 20],
            ['cgpa', 'numerical'],
            ['profile_picture', 'file', 'types' => 'jpg, jpeg, png', 'allowEmpty' => true],
            ['cgpa','filter', 'filter' => convertToNumber],
        );
    }

    public function convertToNumber($value)
    {
        return is_numeric($value) ? (float)$value : $value;
    }
 
    public function attributeLabels()
    {
        return array(
            'roll_no' => 'Roll No',
            'user_id' => 'User ID',
            'cgpa' => 'CGPA',
            'class' => 'Class',
            'hobbies' => 'Hobbies',
        );
    }
    // public function hashPassword($password)
    // {
    //     return password_hash($password, PASSWORD_BCRYPT);
    // }

    public function behaviors()
    {
        return array(
            'EMongoDocumentBehavior' => array(
                'class' => 'ext.YiiMongoDbSuite.behaviors.EMongoDocumentBehavior',
                'arrayDocClassName' => 'Hobby', // Specify the class name of the embedded document
                'arrayPropertyName' => 'hobbies', // Specify the property name for the embedded document
            ),
        );
    }


    // public function checkUniqueEmail($attribute, $params)
    // {
    //     if(!$this->isNewRecord)return true;
    //     $criteria = new EMongoCriteria();
    //     // $criteria->email = new MongoRegex("/^$this->email/i");
    //     $criteria->email = $this->$attribute;
 
        
    //     if (self::model()->count($criteria) > 0) {
    //         $this->addError($attribute, 'This email has already been taken.');
    //         return false;
    //     }
    //     return true;
    // }


    /**
     * Initialize embedded documents
     * This ensures embedded documents are never null when the ORM operates on them
     */

    // public function initEmbeddedDocuments()
    // {
    //     parent::initEmbeddedDocuments();
        
    //     // Initialize address if it's null
    //     if ($this->address === null) {
    //         $this->address = new Address();
    //     }
    // }

    // public function embeddedDocuments()
    // {
    //     return array(
    //         'address' => 'Address',
    //     );
    // }
    
    // public function beforeSave(){
    //     if($this->isNewRecord){
    //         $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    //     }
    //     return parent::beforeSave();
    // }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}