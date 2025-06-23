<!-- 
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1>Update User  echo $model->id; ?></h1>

< $this->renderPartial('_form', array('model'=>$model)); ?> -->

<?php
$this->breadcrumbs=array(
	'Users'=>array('dashboard'),
	$model->first_name=>array('view'),
	'Update',
);

$this->menu=array(
    array('label' => 'Update Profile', 'url' => array('user/update')),
    array('label' => 'View Profile', 'url' => array('user/view')),
    array(
        'label' => 'Delete My Account',
        'url' => array('user/deleteAccount'),
        'confirm' => 'Are you sure you want to delete your account? This cannot be undone.',
    ),
    array('label' => 'Logout', 'url' => array('user/logout')),
)
?>

<h2>Update Profile</h2>

<?php if (Yii::app()->user->hasFlash('success')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'user-update-form',
    'enableAjaxValidation' => false,
)); ?>

<?php echo $form->errorSummary($model); ?>


<p>
    <?php echo $form->labelEx($model, 'email'); ?>
    <?php echo $form->textField($model, 'email',array('disabled' => 'disabled')); ?>
</p>

<p>
    <?php echo $form->labelEx($model, 'first_name'); ?>
    <?php echo $form->textField($model, 'first_name'); ?>
</p>

<p>
    <?php echo $form->labelEx($model, 'last_name'); ?>
    <?php echo $form->textField($model, 'last_name'); ?>
</p>

<p>
    <?php echo CHtml::submitButton('Update Profile'); ?>
</p>

<?php $this->endWidget(); ?>
