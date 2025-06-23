<!-- login.php -->
 
<h2>Login</h2>
 
<?php if (Yii::app()->user->hasFlash('error')): ?>
    <div class="error" style="color: red"><?php echo Yii::app()->user->getFlash('error'); ?></div>
<?php endif; ?>
 
<?php $form = $this->beginWidget('CActiveForm', [
    'id' => 'login-form',
    'enableClientValidation' => true,
    // 'clientOptions' => [
    //     'validateOnSubmit' => true,
    // ],
]); ?>
 
<div>
    <?php echo $form->labelEx($model, 'username'); ?>
    <?php echo $form->textField($model, 'username'); ?>
    <?php echo $form->error($model, 'username'); ?>
 
</div>
 
<div>
    <?php echo $form->labelEx($model, 'password'); ?>
    <?php echo $form->passwordField($model, 'password'); ?>
    <?php echo $form->error($model, 'password'); ?>
 
</div>
 
<div>
    <?php echo $form->labelEx($model, 'rememberMe'); ?>
    <?php echo $form->checkBox($model, 'rememberMe'); ?>
    <?php echo $form->error($model, 'rememberMe'); ?>
</div>
 
<?php echo CHtml::submitButton('Login', ['class' => 'btn btn-primary']); ?>
 
<?php $this->endWidget(); ?>
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#login-form').on('submit', function(e){
            e.preventDefault();
 
            // AJAX request
            $.ajax({
                type: 'POST',
                url: '/index.php/user/login',
                data: $('#login-form').serialize(),
                success: function(response) {
                    console.log(response);
                    if (typeof response === 'string') {
                    response = JSON.parse(response);
                    }
                    if (response.success) {
                        console.log("JWT Token:", response.token);
                        window.location.href = '/index.php/user/index';
                    } else {
                        alert(response.error || "Login failed. Please check your credentials.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                    alert("Something went wrong. Try again.");
                }
            });
        });
    });
</script>
 
<p>Don't have an account?
    <a href="<?php echo Yii::app()->createUrl('user/register'); ?>">Register</a>
</p>
 
 