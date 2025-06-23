<?php


class LoginForm extends CFormModel
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('username, password', 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'login'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'username' => 'Email',
            'password' => 'Password',
            'rememberMe' => 'Remember me',
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     * @param string $attribute the name of the attribute to be validated.
     * @param array $params additional parameters passed with rule when being executed.
     */
    // public function authenticate($attribute, $params)
    // {
    //     if (!$this->hasErrors()) {
    //         $this->_identity = new UserIdentity($this->username, $this->password);
    //         if (!$this->_identity->authenticate()) {
    //             if ($this->_identity->errorCode === UserIdentity::ERROR_USERNAME_INVALID) {
    //                 $this->addError('username', 'Email address is incorrect.');
    //             } else {
    //                 $this->addError('password', 'Password is incorrect.');
    //             }
    //         }
    //     }
    // }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate()) {
                if ($this->_identity->errorCode === UserIdentity::ERROR_USERNAME_INVALID) {
                    $this->addError('username', 'Email address is incorrect.');
                } else {
                    $this->addError('password', 'Password is incorrect.');
                }
                return;
            }
        }
        
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            
            // Store additional user information in session
            Yii::app()->user->setState('user_id', $this->_identity->getId());
            Yii::app()->user->setState('user_email', $this->_identity->getEmail());
            Yii::app()->user->setState('user_type', $this->_identity->getUserType());
            Yii::app()->user->setState('user_login', $this->_identity->getName());
            
            return true;
        } else {
            return false;
        }
    }
}