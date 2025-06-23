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

<style>
/* Tailwind CSS classes for styling */
.max-w-4xl { max-width: 56rem; }
.mx-auto { margin-left: auto; margin-right: auto; }
.bg-white { background-color: #ffffff; }
.shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
.rounded-lg { border-radius: 0.5rem; }
.overflow-hidden { overflow: hidden; }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
.py-4 { padding-top: 1rem; padding-bottom: 1rem; }
.bg-gray-50 { background-color: #f9fafb; }
.border-b { border-bottom-width: 1px; }
.border-gray-200 { border-color: #e5e7eb; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.font-bold { font-weight: 700; }
.text-gray-800 { color: #1f2937; }
.p-6 { padding: 1.5rem; }
.space-y-6 > * + * { margin-top: 1.5rem; }
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.gap-6 { gap: 1.5rem; }
.block { display: block; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.font-medium { font-weight: 500; }
.text-gray-700 { color: #374151; }
.mb-2 { margin-bottom: 0.5rem; }
.w-full { width: 100%; }
.px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
.py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
.border { border-width: 1px; }
.border-gray-300 { border-color: #d1d5db; }
.rounded-md { border-radius: 0.375rem; }
.shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
.focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
.focus\:ring-2:focus { box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); }
.focus\:ring-blue-500:focus { --tw-ring-color: #3b82f6; }
.focus\:border-blue-500:focus { border-color: #3b82f6; }
.text-red-500 { color: #ef4444; }
.mt-1 { margin-top: 0.25rem; }
.text-gray-500 { color: #6b7280; }
.text-xs { font-size: 0.75rem; line-height: 1rem; }
.border-t { border-top-width: 1px; }
.pt-6 { padding-top: 1.5rem; }
.text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.text-gray-900 { color: #111827; }
.mb-4 { margin-bottom: 1rem; }
.hidden { display: none; }
.md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.md\:col-span-2 { grid-column: span 2 / span 2; }
.mt-6 { margin-top: 1.5rem; }
.space-y-3 > * + * { margin-top: 0.75rem; }
.flex { display: flex; }
.gap-3 { gap: 0.75rem; }
.flex-1 { flex: 1 1 0%; }
.bg-red-500 { background-color: #ef4444; }
.text-white { color: #ffffff; }
.hover\:bg-red-600:hover { background-color: #dc2626; }
.mt-3 { margin-top: 0.75rem; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.bg-green-500 { background-color: #10b981; }
.hover\:bg-green-600:hover { background-color: #059669; }
.md\:w-auto { width: auto; }
.bg-blue-600 { background-color: #2563eb; }
.hover\:bg-blue-700:hover { background-color: #1d4ed8; }
.focus\:ring-blue-500:focus { --tw-ring-color: #3b82f6; }
.focus\:ring-offset-2:focus { --tw-ring-offset-width: 2px; }

@media (min-width: 768px) {
    .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .md\:col-span-2 { grid-column: span 2 / span 2; }
    .md\:w-auto { width: auto; }
}
</style>
