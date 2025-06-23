<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">User Registration Form</h2>
    </div>
    
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'user-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions' => array(
            'class' => 'p-6 space-y-6',
            'enctype' => 'multipart/form-data'
        )
    )); ?>
    
        <!-- Basic User Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div>
                <?php echo $form->labelEx($model, 'name', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                <?php echo $form->textField($model, 'name', array(
                    'maxlength' => 100,
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                )); ?>
                <?php echo $form->error($model, 'name', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>
            
            <!-- Email -->
            <div>
                <?php echo $form->labelEx($model, 'email', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                <?php echo $form->emailField($model, 'email', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                )); ?>
                <?php echo $form->error($model, 'email', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Password -->
            <div>
                <?php echo $form->labelEx($model, 'password', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                <?php echo $form->passwordField($model, 'password', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                )); ?>
                <?php echo $form->error($model, 'password', array('class' => 'text-red-500 text-sm mt-1')); ?>
                <p class="text-gray-500 text-xs mt-1">Minimum 6 characters</p>
            </div>
            
            <!-- Role -->
            <div>
                <?php echo $form->labelEx($model, 'role', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                <?php echo $form->dropDownList($model, 'role', array(
                    '' => 'Select Role',
                    'admin' => 'Admin',
                    'teacher' => 'Teacher',
                    'student' => 'Student'
                ), array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'onchange' => 'toggleRoleFields()'
                )); ?>
                <?php echo $form->error($model, 'role', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>
        </div>
        
        <!-- Address Section -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <?php echo CHtml::label('Address Line 1', 'address_line1', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('address[address_line1]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('Address Line 2', 'address_line2', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('address[address_line2]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('City', 'city', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('address[city]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('State', 'state', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('address[state]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('ZIP Code', 'zip', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('address[zip]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('Country', 'country', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('address[country]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
            </div>
        </div>
        
        <!-- Student Fields -->
        <div id="student-fields" class="border-t pt-6 hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <?php echo CHtml::label('Roll Number', 'roll_no', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('student[roll_no]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('CGPA', 'cgpa', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::numberField('student[cgpa]', '', array(
                        'step' => '0.1',
                        'min' => '0',
                        'max' => '10',
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('Class', 'class', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('student[class]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('Profile Picture', 'profile_picture', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::fileField('student[profile_picture]', '', array(
                        'accept' => 'image/*',
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
            </div>
            
            <!-- Hobbies Section -->
            <div class="mt-6">
                <?php echo CHtml::label('Hobbies', '', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                <div id="hobbies-container" class="space-y-3">
                    <div class="hobby-item flex gap-3">
                        <?php echo CHtml::textField('student[hobbies][0][name]', '', array(
                            'placeholder' => 'Hobby name',
                            'class' => 'flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                        )); ?>
                        <?php echo CHtml::textField('student[hobbies][0][description]', '', array(
                            'placeholder' => 'Description',
                            'class' => 'flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                        )); ?>
                        <?php echo CHtml::button('Remove', array(
                            'type' => 'button',
                            'onclick' => 'removeHobby(this)',
                            'class' => 'px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600'
                        )); ?>
                    </div>
                </div>
                <?php echo CHtml::button('Add Hobby', array(
                    'type' => 'button',
                    'onclick' => 'addHobby()',
                    'class' => 'mt-3 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600'
                )); ?>
            </div>
        </div>
        
        <!-- Teacher Fields -->
        <div id="teacher-fields" class="border-t pt-6 hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Teacher Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <?php echo CHtml::label('Employee ID', 'emp_id', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('teacher[emp_id]', '', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::label('Salary', 'salary', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::numberField('teacher[salary]', '', array(
                        'step' => '0.01',
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
                
                <div class="md:col-span-2">
                    <?php echo CHtml::label('Designation', 'designation', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                    <?php echo CHtml::textField('teacher[designation]', '', array(
                        'maxlength' => 255,
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    )); ?>
                </div>
            </div>
            
            <!-- Classes Section -->
            <div class="mt-6">
                <?php echo CHtml::label('Classes', '', array('class' => 'block text-sm font-medium text-gray-700 mb-2')); ?>
                <div id="classes-container" class="space-y-3">
                    <div class="class-item flex gap-3">
                        <?php echo CHtml::textField('teacher[classes][0]', '', array(
                            'placeholder' => 'Class name',
                            'class' => 'flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                        )); ?>
                        <?php echo CHtml::button('Remove', array(
                            'type' => 'button',
                            'onclick' => 'removeClass(this)',
                            'class' => 'px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600'
                        )); ?>
                    </div>
                </div>
                <?php echo CHtml::button('Add Class', array(
                    'type' => 'button',
                    'onclick' => 'addClass()',
                    'class' => 'mt-3 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600'
                )); ?>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="border-t pt-6">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Register User' : 'Update User', array(
                'class' => 'w-full md:w-auto px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
            )); ?>
        </div>
    
    <?php $this->endWidget(); ?>
</div>

<script>
let hobbyCount = 1;
let classCount = 1;

function toggleRoleFields() {
    const role = document.getElementById('<?php echo CHtml::activeId($model, 'role'); ?>').value;
    const studentFields = document.getElementById('student-fields');
    const teacherFields = document.getElementById('teacher-fields');
    
    // Hide all role-specific fields
    studentFields.classList.add('hidden');
    teacherFields.classList.add('hidden');
    
    // Show relevant fields based on role
    if (role === 'student') {
        studentFields.classList.remove('hidden');
    } else if (role === 'teacher') {
        teacherFields.classList.remove('hidden');
    }
}

function addHobby() {
    const container = document.getElementById('hobbies-container');
    const hobbyItem = document.createElement('div');
    hobbyItem.className = 'hobby-item flex gap-3';
    hobbyItem.innerHTML = `
        <input type="text" name="student[hobbies][${hobbyCount}][name]" placeholder="Hobby name"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <input type="text" name="student[hobbies][${hobbyCount}][description]" placeholder="Description"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="button" onclick="removeHobby(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
    `;
    container.appendChild(hobbyItem);
    hobbyCount++;
}

function removeHobby(button) {
    const container = document.getElementById('hobbies-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

function addClass() {
    const container = document.getElementById('classes-container');
    const classItem = document.createElement('div');
    classItem.className = 'class-item flex gap-3';
    classItem.innerHTML = `
        <input type="text" name="teacher[classes][${classCount}]" placeholder="Class name"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="button" onclick="removeClass(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
    `;
    container.appendChild(classItem);
    classCount++;
}

function removeClass(button) {
    const container = document.getElementById('classes-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}
</script>
