<?php
    /* @var $this UploadController */ // Optional: Helps IDEs with autocompletion
    
    $this->pageTitle = Yii::app()->name . ' - File Upload';
    ?>
    
    <h1>Upload File to S3</h1>
    
    <p>Please select a file to upload.</p>
    
    <div class="form">
    
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'upload-form',
        'action' => $this->createUrl('s3/upload'),
        'method' => 'post',
        'enableAjaxValidation' => false,
    
        // !!! VERY IMPORTANT for file uploads !!!
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    
        <p class="note">Demonstration upload form.</p>
    
        <?php
        // If you *were* using a model and it had errors, you'd display a summary:
        //echo $form->errorSummary($model);
        ?>
    
        <div class="row">
            <?php
            // Generate a label using CHtml helper
            echo CHtml::label('Select file:', 'userfile');
            ?>
            <?php
            // Generate the file input field using CHtml::fileField
            // The first argument 'userfile' is the *name* attribute of the input.
            // This MUST match the key expected in $_FILES within your actionUpload method.
            echo CHtml::fileField('userfile', '', array('id' => 'userfile'));
            // We use CHtml::fileField directly because the controller accesses $_FILES['userfile'].
            // If you had an UploadFormModel $model with a public $file property and rules,
            // you would typically use: echo $form->fileField($model, 'file');
            ?>
            <?php
            // If you *were* using a model, you could display specific errors for the field:
            // echo $form->error($model,'file');
            ?>
        </div>
    
        <div class="row buttons">
            <?php
            // Generate a standard submit button
            echo CHtml::submitButton('Upload File');
            ?>
        </div>
    
    <?php $this->endWidget(); ?>
    
    </div><!-- form -->
    