<?php
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;
 
class UserController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';
 
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }
 
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */ 
    public function accessRules()
    {
        return array(
            array(
                'allow', // allow all users to perform 'index','register' and 'login' actions
                'actions' => array('index','register','login'),
                'users' => array('*'),
            ),
            array(
                'allow', // allow authenticated user to perform 'view', 'update' and 'delete' actions
                'actions' => array('view', 'update','delete'),
                'users' => array('*'),
            ),
            // array(
            //     'allow', // allow admin user to perform 'admin' and 'delete' actions
            //     'actions' => array('admin', 'delete'),
            //     'users' => array('admin'),
            // ),
            // array(
            //     'deny', // deny all users
            //     'users' => array('*'),
            // ),
        );
    }
 
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return User the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = User::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
 
        return $model;
    }
 
    /**
     * Performs the AJAX validation.
      * @param User $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    private function validateJwtToken()
    {
        if (!isset(Yii::app()->request->cookies['jwt_token'])) {
            throw new CHttpException(401, 'JWT token not found in cookies');
        }

        $jwt = Yii::app()->request->cookies['jwt_token']->value;

        try {
            $decoded = JWT::decode($jwt, new Key(Yii::app()->params['jwtSecret'], 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            throw new CHttpException(401, 'Invalid or expired token: ' . $e->getMessage());
        }
    }

    public function actionTest()
    {
        echo "Inside Action test of user controller";
 
        $user = new User();
 
        echo $user;
    }
 
    public function actionRegister()
    {
        $model = new User;
 
        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            $model->password = CPasswordHelper::hashPassword($model->password);
 
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'User registered successfully.');
                $this->redirect(['login']);
            }
        }
 
        $this->render('register', ['model' => $model]);
    }
 
    // actionLogin()
 
    public function actionLogin()
        {
            // if (!empty(Yii::app()->user->getId())) {
                
            //     $this->redirect(['index']);
            // }
    
            $model = new LoginForm;
            if(isset($_POST['LoginForm']))
            {
               
                $model->attributes = $_POST['LoginForm'];
                // $model->username=UtilityHelpers::sanitizeInput(input: $model->username);
                if($model->validate() && $model->login())
                {
                
                    
                $user = User::model()->findByAttributes(['email' => $model->username]);
                
                // print_r(json_encode($user));
                // exit;
                    // $payload = [
                    //     // 'iss' => 'demo', // issuer
                    //     'iat' => time(),
                    //     'exp' => time() + 3600, // token expires in 1 hour
                    //     'uid' => (string) $user->_id,
                    //     'username' => $user->email
                    // ];

                    // $jwt = JWT::encode($payload, Yii::app()->params['jwtSecret'], 'HS256');
                    // $cookie = new CHttpCookie('jwt_token', $jwt);
                    // $cookie->httpOnly = true; // Makes it inaccessible to JavaScript (more secure)
                    // $cookie->secure = isset($_SERVER['HTTPS']); // Send only over HTTPS
                    // $cookie->expire = time() + 3600; // 1 hour expiry

                    // Yii::app()->request->cookies['jwt_token'] = $cookie;

                    Yii::app()->session['uid'] = Yii::app()->user->getId();
                    Yii::app()->session['login_time'] = time();
 
                    if (Yii::app()->request->isAjaxRequest) {
                        if (ob_get_length()) ob_clean();
                        echo CJSON::encode([
                            'success' => true,
                            // 'token' => $jwt
                        ]);
                        exit;
                        Yii::app()->end(); // this stops further rendering/output
                    } else {
                        Yii::app()->user->setFlash('success', 'Login successful.');
                        $this->redirect(['index']);
                    }
                    
                }
                else {
                    if (Yii::app()->request->isAjaxRequest) {
                        if (ob_get_length()) ob_clean();

                        echo CJSON::encode(['success' => false]);
                        Yii::app()->end();
                    }
    
                    Yii::app()->user->setFlash('error', 'Invalid username or password.');
                }
            }
    
            $this->render('login', ['model' => $model]);
        }
 
 
    public function actionLogout()
    {
        unset(Yii::app()->request->cookies['jwt_token']);

        Yii::app()->user->clearStates();
        Yii::app()->user->logout();
        $this->redirect(['login']);
    }
 
    public function actionDashboard()
    {
        if (!Yii::app()->user->getState('user_id')) {
            $this->redirect(['login']);
        }
 
        $this->render('dashboard', [
            'username' => Yii::app()->user->getState('user_login'),
        ]);
    }
    public function actionView()
    {
        $userId = Yii::app()->user->getState('user_id');
        //echo $userId;
        if (!$userId) {
            $this->redirect(['login']);
        }
 
        $model = $this->loadModel($userId);
        $this->render('view', ['model' => $modelg]);
    }
 
    public function actionUpdate()
    {
        $userId = Yii::app()->user->getState('user_id');
        //echo $userId;
        if (!$userId) {
            $this->redirect(['login']);
        }
 
        $model = $this->loadModel($userId);
        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            if (!empty($_POST['User']['password'])) {
                $model->password = CPasswordHelper::hashPassword($_POST['User']['password']);
            }
            if ($model->save()) {
                Yii::app()->user->setState('user_login', $model->first_name . ' ' . $model->last_name);
                Yii::app()->user->setFlash('success', 'Profile updated.');
                $this->redirect(['dashboard']);
            }
        }
 
        $this->render('update', ['model' => $model]);
    }
 
    public function actionDeleteAccount()
    {
        $userId = Yii::app()->user->getState('user_id');
        if (!$userId) {
            $this->redirect(['login']);
        }
 
        $model = $this->loadModel($userId);
        if ($model && $model->delete()) {
            Yii::app()->user->clearStates();
            Yii::app()->user->logout();
            $this->redirect(['login']);
        }
    }
    public function actionIndex()
    {
        $model = new User;
        //$this->performAjaxValidation($model);
        $this->render('index');
 
 
        //Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
 
            //echo "<pre>";print_r($model);exit;
 
    //    $dataProvider = new User;
    //    $this->render('index', array(
    //        'dataProvider' => $dataProvider,
    //    ));
    }
}