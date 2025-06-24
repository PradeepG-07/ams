<?php

use MongoDB\BSON\ObjectId;

/**
 * StudentController - Refactored to use helper classes for business logic
 * 
 * This controller now focuses solely on:
 * - Handling HTTP requests and responses
 * - Calling appropriate helper methods
 * - Managing view rendering
 * - Basic request validation
 */
class AttendanceController extends Controller
{
    /**
     * @var string the default layout for the views
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
            // 'postOnly + delete',
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
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array(
                'allow',
                'actions' => array('dashboard','save','manage','index','create', 'stats', 'daywise', 'profile', 'getProfile', 'uploadProfilePicture', 'removeProfilePicture'),
                'users' => array('*'),
            ),
            array(
                'allow',
                'actions' => array('admin', 'delete', 'update'),
                'users' => array('*'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a list of all students
     */
    public function actionIndex($page = 1)
    {
        try{
            Yii::log("Displaying classes index", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
            $classes = ClassesHelper::listClasses($page);
            $total = ClassesHelper::count();
            $this->sendAjaxResponseIfAjax($classes,$total);
            // echo CJSON::encode(array(
            //     'success' => true,
            //     'total' => $total,
            //     'classes' =>  $classes,
            // ));
            // exit;
            $this->render('index', array(
                'classes' => $classes,
                'total' => $total,
            ));

        }catch(Exception $e){
            Yii::log("Error listing classes: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while listing classes: ' . $e->getMessage());
        }
    }
    public function actionSave(){
        print_r($_POST);
    }

    public function actionManage(){
        $classes = ClassesHelper::getAllClasses();
        $this->render('manage', array(
            'classes' => $classes,
        ));
    }
    
}