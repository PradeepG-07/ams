<?php
/* @var $this UserController */
/* @var $user User */
/* @var $student Student */
/* @var $teacher Teacher */
/* @var $form CActiveForm */
?>

<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New User</h2>

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'user-form',
        'enableAjaxValidation' => false,
        'action' => Yii::app()->createUrl($user->isNewRecord ? 'user/create' : 'user/update', array('id' => $user->_id)),
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => false,
        ),
        'htmlOptions' => array(
            'class' => 'space-y-6',
            'enctype' => 'multipart/form-data'
        ),
    )); ?>

    <!-- User Fields Section -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <?php echo $form->labelEx($user, 'name', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->textField($user, 'name', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500',
                    'placeholder' => 'Enter full name'
                )); ?>
                <?php echo $form->error($user, 'name', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($user, 'email', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->emailField($user, 'email', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500',
                    'placeholder' => 'Enter email address'
                )); ?>
                <?php echo $form->error($user, 'email', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($user, 'password', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->passwordField($user, 'password', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500',
                    'placeholder' => 'Enter password'
                )); ?>
                <?php echo $form->error($user, 'password', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>
        </div>

        <!-- Address Section -->
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Address Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                    <input type="text" value="<?php echo $user->address->address_line1; ?>" name="User[address][address_line1]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter address line 1">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" value="<?php echo $user->address->address_line2; ?>" name="User[address][address_line2]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter address line 2 (optional)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" value="<?php echo $user->address->city; ?>" name="User[address][city]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter city">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" value="<?php echo $user->address->state; ?>" name="User[address][state]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter state">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                    <input type="text" value="<?php echo $user->address->zip; ?>" name="User[address][zip]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter ZIP code">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" value="<?php echo $user->address->country; ?>" name="User[address][country]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter country">
                </div>
            </div>
        </div>
    </div>

    <!-- User Type Selection -->
    <div class="bg-blue-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">User Type</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Select User Type</label>
            <select id="user-type-select" name=User[role] <?php if (isset($user) && $user->role) echo 'value="' . $user->role . '"'; ?> class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Type</option>
                <option value="student" <?php if (isset($user) && $user->role == User::ROLE_STUDENT) echo 'selected'; ?>>Student</option>
                <option value="teacher" <?php if (isset($user) && $user->role == User::ROLE_TEACHER) echo 'selected'; ?>>Teacher</option>
            </select>
        </div>
    </div>

    <!-- Student Fields Section -->
    <div id="student-fields" class="bg-green-50 p-4 rounded-lg" style="display: none;">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Student Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <?php echo $form->labelEx($student, 'roll_no', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->textField($student, 'roll_no', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500',
                    'placeholder' => 'Enter roll number'
                )); ?>
                <?php echo $form->error($student, 'roll_no', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($student, 'cgpa', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->numberField($student, 'cgpa', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500',
                    'placeholder' => 'Enter CGPA',
                    'step' => '0.01',
                    'min' => '0',
                    'max' => '4'
                )); ?>
                <?php echo $form->error($student, 'cgpa', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($student, 'class', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->dropDownList($student, 'class', $classes, array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500',
                    'prompt' => 'Select Class'
                )); ?>
                <?php echo $form->error($student, 'class', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($student, 'profile_picture', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                <?php echo $form->fileField($student, 'profile_picture', array(
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500',
                    'accept' => 'image/*'
                )); ?>
                <?php echo $form->error($student, 'profile_picture', array('class' => 'text-red-500 text-sm mt-1')); ?>
            </div>
        </div>

        <!-- Hobbies Section -->
        <div id="hobbies-container">
            <?php
            $hobbies = isset($student->hobbies) && is_array($student->hobbies) ? $student->hobbies : array();
            $i = 0;
            foreach ($hobbies as $hobby): ?>
                <div class="hobby-item bg-white p-3 border rounded-md mb-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Hobby Name</label>
                            <input type="text" name="Student[hobbies][<?php echo $i; ?>][name]" value="<?php echo CHtml::encode($hobby['name']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter hobby name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                            <textarea name="Student[hobbies][<?php echo $i; ?>][description]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" rows="2" placeholder="Enter hobby description"><?php echo CHtml::encode($hobby['description']); ?></textarea>
                        </div>
                    </div>
                    <button type="button" class="mt-2 text-red-600 hover:text-red-800 text-sm remove-hobby">Remove Hobby</button>
                </div>
            <?php $i++;
            endforeach; ?>
        </div>
        <button type="button" id="add-hobby" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Add Another Hobby</button>


        <!-- Teacher Fields Section -->
        <div id="teacher-fields" class="bg-yellow-50 p-4 rounded-lg" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Teacher Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <?php echo $form->labelEx($teacher, 'emp_id', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                    <?php echo $form->textField($teacher, 'emp_id', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500',
                        'placeholder' => 'Enter employee ID'
                    )); ?>
                    <?php echo $form->error($teacher, 'emp_id', array('class' => 'text-red-500 text-sm mt-1')); ?>
                </div>

                <div>
                    <?php echo $form->labelEx($teacher, 'salary', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                    <?php echo $form->numberField($teacher, 'salary', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500',
                        'placeholder' => 'Enter salary',
                        'min' => '0'
                    )); ?>
                    <?php echo $form->error($teacher, 'salary', array('class' => 'text-red-500 text-sm mt-1')); ?>
                </div>
                <div>
                    <?php echo $form->labelEx($teacher, 'classes', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                    <?php echo $form->listBox($teacher, 'classes', $classes, array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500',
                        'multiple' => 'multiple',
                        'size' => 5 // Optional: shows 5 options visible without scrolling
                    )); ?>
                    <?php echo $form->error($teacher, 'classes', array('class' => 'text-red-500 text-sm mt-1')); ?>
                </div>


                <div class="md:col-span-2">
                    <?php echo $form->labelEx($teacher, 'designation', array('class' => 'block text-sm font-medium text-gray-700 mb-1')); ?>
                    <?php echo $form->textField($teacher, 'designation', array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500',
                        'placeholder' => 'Enter designation'
                    )); ?>
                    <?php echo $form->error($teacher, 'designation', array('class' => 'text-red-500 text-sm mt-1')); ?>
                </div>


            </div>
        </div>

    </div>

    <!-- Submit Button -->
    <div class="flex justify-end pt-6">
        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <?php echo $user->isNewRecord ? 'Create User' : 'Update User'; ?>
        </button>
    </div>
    <?php $this->endWidget(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTypeSelect = document.getElementById('user-type-select');
            const studentFields = document.getElementById('student-fields');
            const teacherFields = document.getElementById('teacher-fields');
            const addHobbyBtn = document.getElementById('add-hobby');
            // const hobbiesContainer = document.getElementById('hobbies-container');
            // let hobbyIndex = 1;

            // Handle user type selection
            userTypeSelect.addEventListener('change', function() {
                const selectedType = this.value;

                studentFields.style.display = 'none';
                teacherFields.style.display = 'none';

                if (selectedType === "<?php echo User::ROLE_STUDENT; ?>") {
                    studentFields.style.display = 'block';
                } else if (selectedType === "<?php echo User::ROLE_TEACHER; ?>") {
                    teacherFields.style.display = 'block';
                }
            });

            // Show fields based on existing user role
            if ('<?php echo isset($user) ? $user->role : ''; ?>' === '<?php echo User::ROLE_STUDENT; ?>') {
                studentFields.style.display = 'block';
            } else if ('<?php echo isset($user) ? $user->role : ''; ?>' === '<?php echo User::ROLE_TEACHER; ?>') {
                teacherFields.style.display = 'block';
            }

            // Add hobby functionality
            //     addHobbyBtn.addEventListener('click', function() {
            //         const hobbyItem = document.createElement('div');
            //         hobbyItem.className = 'hobby-item bg-white p-3 border rounded-md mb-3';
            //         hobbyItem.innerHTML = `
            //     <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            //         <div>
            //             <label class="block text-sm font-medium text-gray-600 mb-1">Hobby Name</label>
            //             <input type="text" name="Student[hobbies][${hobbyIndex}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter hobby name">
            //         </div>
            //         <div>
            //             <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
            //             <textarea name="Student[hobbies][${hobbyIndex}][description]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" rows="2" placeholder="Enter hobby description"></textarea>
            //         </div>
            //     </div>
            //     <button type="button" class="mt-2 text-red-600 hover:text-red-800 text-sm remove-hobby">Remove Hobby</button>
            // `;

            //         hobbiesContainer.appendChild(hobbyItem);
            //         hobbyIndex++;
            //     });

            //     // Remove hobby functionality
            //     hobbiesContainer.addEventListener('click', function(e) {
            //         if (e.target.classList.contains('remove-hobby')) {
            //             e.target.closest('.hobby-item').remove();
            //         }
            //     });

            const hobbiesContainer = document.getElementById('hobbies-container');
            let hobbyIndex = hobbiesContainer.querySelectorAll('.hobby-item').length;

            // Add hobby functionality
            document.getElementById('add-hobby').addEventListener('click', function() {
                const hobbyItem = document.createElement('div');
                hobbyItem.className = 'hobby-item bg-white p-3 border rounded-md mb-3';
                hobbyItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Hobby Name</label>
                    <input type="text" name="Student[hobbies][${hobbyIndex}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter hobby name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="Student[hobbies][${hobbyIndex}][description]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" rows="2" placeholder="Enter hobby description"></textarea>
                </div>
            </div>
            <button type="button" class="mt-2 text-red-600 hover:text-red-800 text-sm remove-hobby">Remove Hobby</button>
        `;
                hobbiesContainer.appendChild(hobbyItem);
                hobbyIndex++;
            });

            // Remove hobby functionality
            hobbiesContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-hobby')) {
                    e.target.closest('.hobby-item').remove();
                }
            });
        });
    </script>