<h2>Welcome to your Dashboard</h2>

<p>Hello, <strong><?php echo CHtml::encode($username); ?></strong></p>
<?php
$this->breadcrumbs=array(
	'Users',
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

<hr>

<p>This is your secure area. You can put more user-specific details here.</p>


<?php
echo '<pre>';
print_r(Yii::app()->user->getFlash('user-login'));
echo '</pre>';
