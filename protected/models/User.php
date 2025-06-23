<?php
 
class User extends EMongoDocument
{
    const ROLE_ADMIN = 'admin';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STUDENT = 'student';

    public $name;
    public $email;
    public $password;
    public $role;
    public $address = [];
    public $created_at;
    public $updated_at;
 
    public function getCollectionName()
    {
        return 'users';
    }

    public function rules()
    {
        return array(
            array('name, email, password, role', 'required'),
            array('email', 'email'),
            array('email', 'checkUniqueEmail', 'on' => 'insert'),
            array('name', 'length', 'max' => 100),
            array('password', 'length', 'min' => 6),
            array('role', 'in', 'range' => array(User::ROLE_ADMIN, User::ROLE_TEACHER, User::ROLE_STUDENT)),
            array('name, email, password, role, address, created_at, updated_at', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Full Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'role' => 'Role',
            'address' => 'Address',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        );
    }

    public function behaviors()
    {
        return array(
            'EMongoDocumentBehavior' => array(
                'class' => 'ext.YiiMongoDbSuite.behaviors.EMongoDocumentBehavior',
                'arrayDocClassName' => 'Address',
                'arrayPropertyName' => 'address',
            ),
        );
    }

    public function checkUniqueEmail($attribute, $params)
    {
        if (!$this->isNewRecord) return true;
        
        $criteria = new EMongoCriteria();
        $criteria->email = $this->$attribute;
        
        if (self::model()->count($criteria) > 0) {
            $this->addError($attribute, 'This email has already been taken.');
            return false;
        }
        return true;
    }

    public function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->password = CPasswordHelper::hashPassword($this->password);
            $this->created_at = new MongoDate();
        }
        $this->updated_at = new MongoDate();
        
        return parent::beforeSave();
    }

    public function initEmbeddedDocuments()
    {
        parent::initEmbeddedDocuments();
        
        // Initialize address array if it's null
        if ($this->address === null) {
            $this->address = array();
        }
    }

    public function embeddedDocuments()
    {
        return array(
            'address' => 'Address',
        );
    }

    public function verifyPassword($password)
    {
        return CPasswordHelper::verifyPassword($password, $this->password);
    }
     
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}