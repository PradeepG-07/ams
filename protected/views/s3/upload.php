    <!-- filepath: /home/dhanunjaya.s/Projects/yii/AMS/protected/views/s3/upload.php -->
    <?php
    /* @var $this UploadController */
    
    $this->pageTitle = Yii::app()->name . ' - File Upload';
    ?>
    
    <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Upload File to S3</h1>
    
        <p class="text-gray-600 mb-4">Please select a file to upload.</p>
    
        <div class="form">
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'upload-form',
                'action' => $this->createUrl('s3/upload'),
                'method' => 'post',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('enctype' => 'multipart/form-data'),
            ));
            ?>
    
            <div class="mb-4">
                <?php
                echo CHtml::label('Select file:', 'userfile', array('class' => 'block text-sm font-medium text-gray-700 mb-2'));
                echo CHtml::fileField('userfile', '', array(
                    'id' => 'userfile',
                    'class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-blue-500 focus:border-blue-500'
                ));
                ?>
            </div>
    
            <div class="mt-6">
                <?php
                echo CHtml::submitButton('Upload File', array(
                    'class' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
                ));
                ?>
            </div>
    
            <?php $this->endWidget(); ?>
        </div>
    </div>