<?php

/**
 * AuthController - Refactored to use helper classes for business logic
 * 
 * This controller now focuses solely on:
 * - Handling HTTP requests and responses
 * - Calling appropriate helper methods
 * - Managing view rendering and redirects
 * - Basic request validation
 */
class AuthController extends Controller
{
    /**
     * @var string the default layout for the views
     */
    public $layout = '//layouts/column1';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('login'),
                'users' => array('*'),
            ),
            array(
                'allow',
                'actions' => array('logout'),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays the login page and handles authentication
     */
    public function actionLogin()
    {
        try {
            // Redirect if user is already logged in
            
            if (!Yii::app()->user->isGuest) {
                $this->redirectBasedOnRole();
                return;
            }

            $model = new LoginForm;

            // Handle AJAX validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }

            // Handle form submission
            if (isset($_POST['LoginForm'])) {
                $model->attributes = $_POST['LoginForm'];
                
                // Validate form input
                if ($model->validate() && $model->login()) {      
                    Yii::log('User logged in successfully: ' . $model->username, 'info', 'AuthController');              
                    $this->redirectBasedOnRole();
                    return;
                } else {
                    Yii::log('Login form validation failed for user: ' . $model->username, 'warning', 'AuthController');
                }
            }

            // Display the login form
            $this->render('login', array('model' => $model));

        } catch (Exception $e) {
            Yii::log('Error in login action: ' . $e->getMessage(), 'error', 'AuthController');
            
            // Create a new model to avoid potential issues
            $model = new LoginForm;
            $model->addError('username', 'An error occurred during login. Please try again.');
            
            $this->render('login', array('model' => $model));
        }
    }

    /**
     * Logs out the current user and redirect to homepage
     */
    public function actionLogout()
    {
        try {
            // Get current user info for logging
            $userId = Yii::app()->user->getId();
            $userType = Yii::app()->user->getState('user_type');
            
            // Logout user using helper
            $logoutResult = AuthenticationHelper::logoutUser();
            
            if ($logoutResult['success']) {
                Yii::log("User logged out successfully: {$userId} ({$userType})", 'info', 'AuthController');
            } else {
                Yii::log("Logout warning: {$logoutResult['message']}", 'warning', 'AuthController');
            }

            // Redirect to home page regardless of logout result
            // $this->redirect(Yii::app()->homeUrl);
            $this->redirect(Yii::app()->homeUrl. '/auth/login');


        } catch (Exception $e) {
            Yii::log('Error during logout: ' . $e->getMessage(), 'error', 'AuthController');
            
            // Force logout even if helper fails
            Yii::app()->user->logout();
            $this->redirect(Yii::app()->homeUrl);
        }
    }
    
    /**
     * Redirects user based on their role using authentication helper
     */
    private function redirectBasedOnRole()
    {
        
        try {
            $userType = Yii::app()->user->getState('user_type');
            
            if (empty($userType)) {
                Yii::log('User type not found in session during redirect', 'warning', 'AuthController');
                $this->redirect(Yii::app()->homeUrl);
                return;
            }

            // Get redirect URL based on user type
            switch ($userType) {
                
                case 'admin':
                    $redirectUrl = Yii::app()->createUrl('/admin/managestudents');
                    break;
                case 'teacher':
                    $redirectUrl = Yii::app()->createUrl('/teacher/classes');
                    break;
                case 'student':
                    $redirectUrl = Yii::app()->createUrl('/student/dashboard');
                    break;
                default:
                    Yii::log('Unknown user type during redirect: ' . $userType, 'warning', 'AuthController');
                    $redirectUrl = Yii::app()->homeUrl;
                    break;
            }
            // echo "Redirecting to: " . $redirectUrl;
            // exit;
            $this->redirect($redirectUrl);

            

        } catch (Exception $e) {
            Yii::log('Error during role-based redirect: ' . $e->getMessage(), 'error', 'AuthController');
            $this->redirect(Yii::app()->homeUrl);
        }
    }
}