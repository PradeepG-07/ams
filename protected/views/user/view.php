<?php
/* @var $this UserController */
/* @var $model User */

// $this->breadcrumbs=array(
// 	'Users'=>array('index'),
// 	$model->id,
// );

// $this->menu=array(
// 	array('label'=>'List User', 'url'=>array('index')),
// 	array('label'=>'Create User', 'url'=>array('create')),
// 	array('label'=>'Update User', 'url'=>array('update', 'id'=>$model->id)),
// 	array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
// 	array('label'=>'Manage User', 'url'=>array('admin')),
// );
?>

<?php
$this->breadcrumbs=array(
	'Users'=>array('dashboard'),
	$model->first_name=>array('view')
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

<h1>View User #<?php echo $model->first_name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'first_name',
		'last_name',
		'email',
	),
)); ?>
