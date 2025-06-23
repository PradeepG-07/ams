<?php
 
class SiteController extends CController{
    
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            $this->render('error', $error);
        }
    }

    public function actionTest()
    {
        echo "Test action";
        exit;
    }

}