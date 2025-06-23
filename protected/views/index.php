<?php
// Check if the user is a guest
if (Yii::app()->user->isGuest) {
    // Redirect to the login page
    $this->redirect(Yii::app()->createUrl('site/login'));
    Yii::app()->end();
}

// Redirect based on the user's role
$role = Yii::app()->user->role; // Assuming 'role' is stored in the user session

switch ($role) {
    case 'admin':
        $this->redirect(Yii::app()->createUrl('admin/dashboard'));
        break;
    case 'teacher':
        $this->redirect(Yii::app()->createUrl('teacher/index'));
        break;
    case 'student':
        $this->redirect(Yii::app()->createUrl('student/dashboard'));
        break;
    default:
        // Redirect to a default page if the role is not recognized
        $this->redirect(Yii::app()->homeUrl);
        break;
}

Yii::app()->end();
?>