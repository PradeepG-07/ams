<?php

use MongoDB\BSON\ObjectId;

/**
 * TeacherController - Refactored to use helper classes for business logic
 * 
 * This controller now focuses solely on:
 * - Handling HTTP requests and responses
 * - Calling appropriate helper methods
 * - Managing view rendering
 * - Basic request validation
 */
class TeacherController extends Controller
{
    public $layout = '//layouts/column2';

    public function filters()
    {
        return array(
            'accessControl',
            'postOnly + delete',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index', 'view'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('classes', 'classDetails', 'attendance', 'login', 'saveAttendance'),
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>array('admin', 'delete'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }
 /**
     * Displays a list of all teachers
     */
    public function actionIndex($page = 1)
    {
        try{
            Yii::log("Displaying teacher index", CLogger::LEVEL_INFO, 'application.controllers.TeacherController');
            $teachers = TeacherHelper::listTeachers($page);
            $total = TeacherHelper::count();
            $this->sendAjaxResponseIfAjax($teachers,$total);
            // echo CJSON::encode(array(
            //     'success' => true,
            //     'total' => $total,
            //     'teachers' =>  $teachers,
            // ));
            $this->render('index', array(
                'teachers' => $teachers,
                'total' => $total,
            ));

        }catch(Exception $e){
            Yii::log("Error listing teachers: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.TeacherController');
            throw new CHttpException(500, 'An error occurred while listing teachers: ' . $e->getMessage());
        }
    }

    private function sendAjaxResponseIfAjax($teachers,$total)
    {
        if (Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode(array(
                'success' => true,
                'total' => $total,
                'teachers' =>  $teachers,
            ));
            Yii::app()->end();
        }
    }
}