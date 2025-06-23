<?php
/* @var $this TeacherController */
/* @var $teacher array */

$this->breadcrumbs = array(
    'Teacher' => array('index'),
    'Classes',
);
?>

<head>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<div class="bg-white p-6 rounded-lg shadow-sm">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Classes for <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></h1>

    <div class="classes-list">
        <?php if (empty($teacher['classes'])): ?>
            <p class="text-gray-600 text-lg">No classes assigned to this teacher.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($teacher['classes'] as $class): ?>
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 overflow-hidden border border-gray-100">
                        <div class="p-5">
                            <h5 class="text-xl font-semibold text-gray-800 mb-3"><?php echo CHtml::encode($class['class_name']); ?></h5>
                            <div class="mb-4 text-gray-600">
                                <p class="flex items-center mb-2">
                                    <span class="font-medium mr-2">Subject:</span> 
                                    <?php echo CHtml::encode($class['subject']); ?>
                                </p>
                                <p class="flex items-center">
                                    <span class="font-medium mr-2">Academic Year:</span> 
                                    <?php echo CHtml::encode($class['academic_year']); ?>
                                </p>
                            </div>
                            <a href="<?php echo $this->createUrl('teacher/classDetails', array('id' => (string)$class['_id'])); ?>" 
                               class="inline-block w-full text-center py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-300">
                                View Class Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
