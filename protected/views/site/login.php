<?php
/* @var $this TeacherController */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Login';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden mt-10">
    <div class="px-6 py-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login to Your Account</h2>

        <div class="mb-4">
            <?php if(Yii::app()->user->hasFlash('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo Yii::app()->user->getFlash('error'); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'login-form',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
            'htmlOptions' => array(
                'class' => 'space-y-6',
            ),
        )); ?>

        <div class="space-y-4">
            <div>
                <?php echo $form->labelEx($model, 'email', array('class' => 'block text-sm font-medium text-gray-700')); ?>
                <?php echo $form->textField($model, 'email', array(
                    'class' => 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500',
                    'placeholder' => 'Email Address'
                )); ?>
                <?php echo $form->error($model, 'email', array('class' => 'text-red-600 text-sm mt-1')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model, 'password', array('class' => 'block text-sm font-medium text-gray-700')); ?>
                <?php echo $form->passwordField($model, 'password', array(
                    'class' => 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500',
                    'placeholder' => 'Password'
                )); ?>
                <?php echo $form->error($model, 'password', array('class' => 'text-red-600 text-sm mt-1')); ?>
            </div>

            <div class="flex items-center">
                <?php echo $form->checkBox($model, 'rememberMe', array('class' => 'h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded')); ?>
                <?php echo $form->label($model, 'rememberMe', array('class' => 'ml-2 block text-sm text-gray-700')); ?>
                <?php echo $form->error($model, 'rememberMe', array('class' => 'text-red-600 text-sm mt-1')); ?>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Sign In
            </button>
        </div>

        <?php $this->endWidget(); ?>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <p class="text-center text-sm text-gray-600">
            Need help? Contact the system administrator.
        </p>
    </div>
</div>
