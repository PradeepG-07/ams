<?php
 $this->breadcrumbs=array(
	'Users'=>array('index'),
    'Register',
);
?>
<h2>Register</h2>

<?php $form = $this->beginWidget('CActiveForm'); ?>

<?php echo $form->errorSummary($model); ?>

<p>
    <?php echo $form->labelEx($model, 'First Name'); ?>
    <?php echo $form->textField($model, 'first_name'); ?>
</p>
<p>
    <?php echo $form->labelEx($model, 'Last Name'); ?>
    <?php echo $form->textField($model, 'last_name'); ?>
</p>

<p>
    <?php echo $form->labelEx($model, 'email'); ?>
    <?php echo $form->textField($model, 'email'); ?>
</p>

<p>
    <?php echo $form->labelEx($model, 'password'); ?>
    <?php echo $form->passwordField($model, 'password'); ?>
</p>

<p>
    <?php echo CHtml::submitButton('Register'); ?>
</p>

<?php $this->endWidget(); ?>
