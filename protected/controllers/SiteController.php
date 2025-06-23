<?php
 
class SiteController extends Controller
{
    public $layout = '//layouts/main';
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }
 
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        try {
            Yii::log("Starting actionIndex", CLogger::LEVEL_INFO, 'application.site.index');
            $this->render('index');
        } catch (Exception $e) {
            Yii::log("Error in actionIndex: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.site.index');
            $this->render('error', array(
                'code' => 500,
                'message' => 'An error occurred while processing your request.'
            ));
        }
    }
 
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        try {
            Yii::log("Starting actionError", CLogger::LEVEL_INFO, 'application.site.error');
            
            if ($error = Yii::app()->errorHandler->error) {
                Yii::log("Handling error: code={$error['code']}, message={$error['message']}", CLogger::LEVEL_WARNING, 'application.site.error');
                
                if (Yii::app()->request->isAjaxRequest)
                    echo $error['message'];
                else
                    $this->render('error', array(
                        'code' => $error['code'],
                        'message' => $error['message']
                    ));
            } else {
                Yii::log("No error information available", CLogger::LEVEL_TRACE, 'application.site.error');
            }
        } catch (Exception $e) {
            Yii::log("Error in actionError: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.site.error');
            echo 'An error occurred while processing the error page.';
        }
    }
 
    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        try {
            Yii::log("Starting actionLogin", CLogger::LEVEL_INFO, 'application.site.login');
            
            $model = new LoginForm;
 
            // if it is ajax validation request
            $this->performAjaxValidation($model);
 
            // collect user input data
            if (isset($_POST['LoginForm'])) {
                Yii::log("Processing login form submission", CLogger::LEVEL_TRACE, 'application.site.login');
                $model->attributes = $_POST['LoginForm'];
                // validate user input and redirect to the previous page if valid
                if ($model->validate() && $model->login()) {
                    Yii::log("User successfully logged in: " . Yii::app()->user->name, CLogger::LEVEL_INFO, 'application.site.login');
                    Yii::app()->user->setFlash('success', 'Welcome back, ' . Yii::app()->user->name . '!');
                    $this->redirect(Yii::app()->user->returnUrl);
                } else {
                    Yii::log("Login failed for username: " . $model->email, CLogger::LEVEL_WARNING, 'application.site.login');
                }
            }
            
            // display the login form
            Yii::log("Rendering login form", CLogger::LEVEL_TRACE, 'application.site.login');
            $this->render('login', array('model' => $model));
        } catch (Exception $e) {
            Yii::log("Error in actionLogin: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.site.login');
            Yii::app()->user->setFlash('error', 'An error occurred during login.');
            print_r($e->getMessage());
            // $this->redirect(array('site/index'));
        }
    }
 
    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        try {
            Yii::log("Starting actionLogout for user: " . Yii::app()->user->name, CLogger::LEVEL_INFO, 'application.site.logout');
            
            Yii::app()->user->logout();
            Yii::log("User logged out successfully", CLogger::LEVEL_INFO, 'application.site.logout');
            
            Yii::app()->user->setFlash('success', 'You have been successfully logged out.');
            $this->redirect(Yii::app()->homeUrl);
        } catch (Exception $e) {
            Yii::log("Error in actionLogout: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.site.logout');
            Yii::app()->user->setFlash('error', 'An error occurred during logout.');
            $this->redirect(Yii::app()->homeUrl);
        }
    }
 
 
    public function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    
}
 