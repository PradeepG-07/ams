<?php

/**
 * UserIdentity represents the data needed to identity a user.
 */
class UserIdentity extends CUserIdentity
{
    const ERROR_EMAIL_INVALID = 3;
    private $_id;
    private $_email;
    private $_password;
    // private $_token;
    private $_role;
    private $_teacherId;
    private $_studentId;

    private $_studentClass;
    private $_studentClassId;


    public function __construct($email, $password)
    {
        $this->_email = $email;
        $this->_password = $password; 
        parent::__construct($email, $password);
    }
    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
{
   
    //Check for the login details of the user
    $user = User::model()->findByAttributes(array('email' => $this->_email));
   
    if($user === null) {
        $this->errorCode = self::ERROR_EMAIL_INVALID;
        return !$this->errorCode;
    }else if($this->validatePassword($user->password) === false) {
        $this->errorCode = self::ERROR_PASSWORD_INVALID;
        return !$this->errorCode;
    }else{
        $this->_id = (string)$user->_id;
        $this->_email = $user->email;
        $this->_role = $user->role;
        
        // Handle role-specific logic BEFORE setting error code
        switch ($user->role) {
            case User::ROLE_ADMIN:
                // Admin logic can be handled here if needed
                break;
            case User::ROLE_TEACHER:
                // Teacher logic can be handled here if needed
                {
                    $teacher = TeacherHelper::loadTeacherByUserId($user->_id);
                    if ($teacher) {
                        $this->_teacherId = (string)$teacher->_id;
                    }
                }
                break;
            case User::ROLE_STUDENT:
                // Student logic can be handled here if needed
                {
                    $student = StudentHelper::loadStudentByUserId($user->_id);
                    if ($student) {
                        $this->_studentId = (string)$student->_id;
                        
                        // Handle class loading gracefully - class might not exist or be empty
                        if (!empty($student->class)) {
                            try {
                                $cur = ClassesHelper::loadClassById($student->class);
                                if ($cur) {
                                    $this->_studentClassId = (string)$cur->_id; // Store class ID
                                    $this->_studentClass = $cur->class_name;
                                } else {
                                    $this->_studentClassId = null; // Class not found
                                    $this->_studentClass = null; // Class not found
                                }
                            } catch (CHttpException $e) {
                                // Class doesn't exist - log warning and set to null
                                Yii::log("Student {$student->_id} has invalid class ID: {$student->class}", CLogger::LEVEL_WARNING, 'application.components.UserIdentity');
                                $this->_studentClass = null;
                                $this->_studentClassId = null; // Class not found
                            }
                        } else {
                            // Student has no class assigned
                            $this->_studentClass = null;
                            $this->_studentClassId = null; // Class not found
                        }
                    }
                }
                break;
            default:
                $this->errorCode = self::ERROR_EMAIL_INVALID;
                return !$this->errorCode;
        }
        
        $this->errorCode = self::ERROR_NONE;
        return !$this->errorCode;
    }
}
    
    /**
     * Validate password (implement your own logic)
     */
    private function validatePassword($storedPassword)
    {
        return CPasswordHelper::verifyPassword($this->_password, $storedPassword);
    }

    /**
     * @return mixed user ID
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getEmail()
    {
        return $this->_email;
    }
    
    /**
     * @return string JWT token
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @return string user role
     */
    public function getUserRole()
    {
        return $this->_role;
    }

    /**
     * @return string teacher ID if user is a teacher
     */
    public function getTeacherId()
    {
        if ($this->_role === User::ROLE_TEACHER) {
            return $this->_teacherId; 
        }
        return null; // Not a teacher
    }
    /**
     * @return string student ID if user is a student
     */
    public function getStudentId()
    {
        if ($this->_role === User::ROLE_STUDENT) {
            return $this->_studentId;
        }
        return null; // Not a student
    }

    public function getStudentClass()
    {
        if ($this->_role === User::ROLE_STUDENT) {
            return $this->_studentClass;
        }
        return null; // Not a student
    }

    public function getStudentClassId()
    {
        if ($this->_role === User::ROLE_STUDENT) {
            return $this->_studentClassId;
        }
        return null; // Not a student
    }
}