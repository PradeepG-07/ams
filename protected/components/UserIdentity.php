<?php

/**
 * UserIdentity represents the data needed to identity a user.
 */
class UserIdentity extends CUserIdentity
{
    private $_id;
    private $_email;
    private $_token;
    private $_userType;

    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        // Check for admin credentials (hardcoded or from config)
        $adminUsername = Yii::app()->params['adminUsername'] ?? 'admin';
        $adminPassword = Yii::app()->params['adminPassword'] ?? 'admin123'; // You should use a secure password
        
        if ($this->username === $adminUsername) {
            if ($this->password === $adminPassword) {
                $this->_id = 'admin';
                $this->_email = Yii::app()->params['adminEmail'] ?? 'admin@example.com';
                $this->_userType = 'admin';
                $this->errorCode = self::ERROR_NONE;
            } else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                return !$this->errorCode;
            }
        } else {
            // Try to find a teacher
            // echo $this->username;
            $teacher = Teacher::model()->findByAttributes(array('email' => $this->username));
            // print_r(json_encode($teacher,JSON_PRETTY_PRINT));
            if ($teacher !== null && $this->validatePassword($teacher->password)) {
                
                $this->_id = (string)$teacher->_id;
                $this->_email = $teacher->email;
                $this->username = $teacher->email;
                $this->_userType = 'teacher';
                $this->errorCode = self::ERROR_NONE;
            } else {
                // Try to find a student
                $student = Student::model()->findByAttributes(array('email' => $this->username));
                
                if ($student !== null && $this->validatePassword($student->password)) {
                    $this->_id = (string)$student->_id;
                    $this->_email = $student->email;
                    $this->username = $student->email;
                    $this->_userType = 'student';
                    $this->errorCode = self::ERROR_NONE;
                } else {
                    $this->errorCode = self::ERROR_USERNAME_INVALID;
                    return !$this->errorCode;
                }
            }
        }
        
        // Generate JWT token
        $this->_token = JWTHelper::generateToken($this->_id, $this->_email, $this->_userType);
        
        // Store user data in session
        $this->setState('user_id', (string)$this->_id);
        $this->setState('username', $this->username);
        $this->setState('email', $this->_email);
        $this->setState('user_type', $this->_userType);
        $this->setState('jwt_token', $this->_token);
        
        return !$this->errorCode;
    }
    
    /**
     * Validate password (implement your own logic)
     */
    private function validatePassword($storedPassword)
    {
        // For hashed passwords, use password_verify
        return password_verify($this->password, $storedPassword);
        
        // For plain passwords (not recommended for production)
        // return $storedPassword === $this->password;
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
     * @return string user type
     */
    public function getUserType()
    {
        return $this->_userType;
    }
}