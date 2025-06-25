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
class ClassesController extends Controller
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
            'postOnly + delete',
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
                'actions' => array('create', 'delete', 'update'),
                'expression' => "Yii::app()->user->isAdmin()",
            ),
            array(
                'allow',
                'actions' => array('getClass', 'index'),
                'expression' => "Yii::app()->user->isAdmin() || Yii::app()->user->isTeacher() || Yii::app()->user->isStudent()",
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


            // echo Yii::app()->user->isTeacher();
            // exit;
            if(Yii::app()->user->isAdmin()){
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
            }
            elseif(Yii::app()->user->isTeacher()){
                $teacherId = Yii::app()->user->getState("teacher_id");
                $teacherId = new ObjectId($teacherId);
                $classes = TeacherHelper::listClasses($teacherId, $page);
                $total = count(TeacherHelper::loadTeacherById($teacherId)->classes);
                // print_r($classes);
                // exit;
                // $data = [1, 2, 3, 4, 5];
                // $data = array_map(fn($x) => $x * $x, $data);
                // print_r($data);
                // exit; 
                $this->sendAjaxResponseIfAjax($classes,$total);
                // echo CJSON::encode([
                //     'success' => true,
                //     'classes' => $data
                // ]);
                $this->render('index', array(
                    'classes' => $classes,
                    'total' => $total,
                ));
                // $this->
            }
            // exit;

           
        }catch(Exception $e){
            Yii::log("Error listing classes: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while listing classes: ' . $e->getMessage());
        }
    }

    public function actionCreate(){
        try {
            Yii::log("Creating new class", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
            $model = new Classes();
            if (isset($_POST['Classes'])) {
               $result = ClassesHelper::createClass($_POST['Classes']);
                if ($result['success']) {
                    Yii::log("Class created successfully", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
                    $this->redirect(array('classes/index'));

                } else {
                    Yii::log("Failed to create class: " . json_encode($result['message']), CLogger::LEVEL_WARNING, 'application.controllers.ClassesController');
                    $model->addErrors($result['model']->getErrors());
                }
            }
            $this->render('create', array('model' => $model));
        } catch (Exception $e) {
            Yii::log("Error creating class: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while creating the class: ' . $e->getMessage());
        }
    }
    public function actionUpdate($id)
    {
        try {
            Yii::log("Updating class with ID: $id", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
            $id = new ObjectId($id);
            $model = ClassesHelper::loadClassById($id);
            if (isset($_POST['Classes'])) {
                $result = ClassesHelper::updateClass($id, $_POST['Classes']);
                if ($result['success']) {
                    Yii::log("Class updated successfully", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
                    $this->redirect(array('classes/index'));
                } else {
                    Yii::log("Failed to update class: " . json_encode($result['message']), CLogger::LEVEL_WARNING, 'application.controllers.ClassesController');
                    $model->addErrors($result['model']->getErrors());
                }
            }
            $this->render('update', array('model' => $model));
        } catch (Exception $e) {
            Yii::log("Error updating class: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while updating the class: ' . $e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        try {
            Yii::log("Deleting class with ID: $id", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
            $id = new ObjectId($id);
            if (ClassesHelper::deleteClass($id)) {
                Yii::log("Class deleted successfully", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
                if (!Yii::app()->request->isAjaxRequest) {
                    $this->redirect(array('index'));
                } else {
                    echo CJSON::encode(array('success' => true));
                    Yii::app()->end();
                }
            } else {
                Yii::log("Failed to delete class with ID: $id", CLogger::LEVEL_WARNING, 'application.controllers.ClassesController');
                throw new CHttpException(500, 'An error occurred while deleting the class.');
            }
        } catch (Exception $e) {
            if($e instanceof CHttpException) {
                throw $e; // Re-throw if it's already a CHttpException
            }
            Yii::log("Error deleting class: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while deleting the class: ' . $e->getMessage());
        }
    }

    private function sendAjaxResponseIfAjax($classes,$total)
    {
        if (Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode(array(
                'success' => true,
                'total' => $total,
                'classes' =>  $classes,
            ));
            Yii::app()->end();
        }
    }

    

    public function actionGetClass($className){
        try {
            Yii::log("Fetching students for class ID: $className", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
            $students = StudentHelper::getStudentsFromClassName($className);
            if ($students) {
                Yii::log("Students fetched successfully for class name: $className", CLogger::LEVEL_INFO, 'application.controllers.ClassesController');
                $this->redirect('classlist', [
                    'students' => $students,
                    'className' => $className,
                ]);
                Yii::app()->end();
            } else {
                Yii::log("No students found for class name: $className", CLogger::LEVEL_WARNING, 'application.controllers.ClassesController');
                throw new CHttpException(404, 'No students found for the specified class.');
            }
        } catch (Exception $e) {
            Yii::log("Error fetching students for class name: $className - " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.controllers.ClassesController');
            throw new CHttpException(500, 'An error occurred while fetching students for the class: ' . $e->getMessage());
        }
    }
}