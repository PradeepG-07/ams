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
            switch (Yii::app()->user->getRole()) {
                case User::ROLE_ADMIN:
                    Yii::log("User is admin, redirecting to admin dashboard", CLogger::LEVEL_INFO, 'application.site.index');
                    $this->redirect(array('student/index'));
                    break;
                case User::ROLE_STUDENT:
                    Yii::log("User is student, redirecting to student dashboard", CLogger::LEVEL_INFO, 'application.site.index');
                    $this->redirect(array('student/dashboard'));
                    break;
                case User::ROLE_TEACHER:
                    Yii::log("User is teacher, redirecting to teacher dashboard", CLogger::LEVEL_INFO, 'application.site.index');
                    $this->redirect(array('teacher/index'));
                    break;
                default:
                    Yii::log("Unknown user role, redirecting to login page", CLogger::LEVEL_WARNING, 'application.site.index');
                    $this->redirect(array('site/login'));
            }
            // $this->render('index');
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

            if( !Yii::app()->user->isGuest ) {
                Yii::log("User is already logged in, redirecting to home", CLogger::LEVEL_INFO, 'application.site.login');
                if(Yii::app()->user->isTeacher()) {
                    Yii::log("Redirecting teacher to dashboard", CLogger::LEVEL_INFO, 'application.site.login');
                    $this->redirect(array('classes/index'));
                } elseif(Yii::app()->user->isStudent()) {
                    Yii::log("Redirecting student to dashboard", CLogger::LEVEL_INFO, 'application.site.login');
                    $this->redirect(array('student/dashboard'));
                } elseif(Yii::app()->user->isAdmin()) {
                    Yii::log("Redirecting admin to dashboard", CLogger::LEVEL_INFO, 'application.site.login');
                    $this->redirect(array('student/index'));
                }
                Yii::log("Redirecting to home URL", CLogger::LEVEL_INFO, 'application.site.login');
                // Redirect to home URL if already logged in
                $this->redirect(Yii::app()->homeUrl);
            }

            $model = new LoginForm;

            // if it is ajax validation request
            $this->performAjaxValidation($model);

            // collect user input data
            if (isset($_POST['LoginForm'])) {
                Yii::log("Processing login form submission", CLogger::LEVEL_TRACE, 'application.site.login');
                $model->attributes = $_POST['LoginForm'];

                if ($model->validate() && $model->login()) {
                    Yii::log("User successfully logged in: " . Yii::app()->user->name, CLogger::LEVEL_INFO, 'application.site.login');
                    Yii::app()->user->setFlash('success', 'Welcome back, ' . Yii::app()->user->name . '!');
                    if(Yii::app()->user->isTeacher()) {
                        Yii::log("Redirecting teacher to dashboard", CLogger::LEVEL_INFO, 'application.site.login');
                        $this->redirect(array('classes/index'));
                    } elseif(Yii::app()->user->isStudent()) {
                        Yii::log("Redirecting student to dashboard", CLogger::LEVEL_INFO, 'application.site.login');
                        $this->redirect(array('student/dashboard'));
                    } elseif(Yii::app()->user->isAdmin()) {
                        Yii::log("Redirecting admin to dashboard", CLogger::LEVEL_INFO, 'application.site.login');
                        $this->redirect(array('student/index'));
                    }
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
