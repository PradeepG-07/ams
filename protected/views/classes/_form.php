<?php
/* @var $this ClassesController */
/* @var $model Classes */
/* @var $form CActiveForm */
?>

<div class="form max-w-lg mx-auto">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'classes-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => false,
        ),
        'htmlOptions' => array('class' => 'space-y-6'),
    )); ?>

    <div class="bg-white shadow-md rounded-lg p-6">
        <?php if ($model->hasErrors()): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-sm text-red-700">
                <?php echo $form->errorSummary($model, 'Please fix the following errors:'); ?>
            </div>
        <?php endif; ?>

        <div class="mb-5">
            <?php echo $form->labelEx($model, 'class_name', array('class' => 'block text-gray-700 text-sm font-bold mb-2')); ?>
            <?php echo $form->textField($model, 'class_name', array(
                'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500',
                'placeholder' => 'Enter class name'
            )); ?>
            <?php echo $form->error($model, 'class_name', array('class' => 'text-red-500 text-xs italic mt-1')); ?>
        </div>

        <!-- <div class="mb-5">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="academic_year">Academic Year</label>
            <?php //echo $form->dropDownList($model, 'academic_year', array(
            //     '2023-2024' => '2023-2024',
            //     '2024-2025' => '2024-2025',
            //     '2025-2026' => '2025-2026',
            // ), array(
            //     'empty' => 'Select Academic Year',
            //     'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500'
            // )); ?>
            <?php //echo $form->error($model, 'academic_year', array('class' => 'text-red-500 text-xs italic mt-1')); ?>
        </div> -->

        <!-- List teachers associated with class if necessary -->

        <div class="mb-5">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array(
                'class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full'
            )); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>