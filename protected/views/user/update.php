<div class="form">
    <?php $this->renderPartial('_form', array(
        'user' => $model,
        'student' => $student,
        'teacher' => $teacher,
        'classes' => $classes,
    )); ?>
</div>