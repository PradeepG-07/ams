<?php
/* @var $this AttendanceController */
/* @var $classes array */

$this->pageTitle = Yii::app()->name . ' - Manage Attendance';
$this->breadcrumbs = array(
    'Attendance' => array('index'),
    'Manage',
);
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Manage Attendance</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Date Selector -->
                <div>
                    <label for="attendance-date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date"  max= <?php echo date('Y-m-d'); ?> id="attendance-date" name="attendance_date"
                        value="<?php echo date('Y-m-d'); ?>"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- Class Selector -->
                <div>
                    <label for="class-selector" class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                    <select id="class-selector" name="class_id"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">-- Select a Class --</option>
                        <?php foreach ($classes as $id => $className): ?>
                            <option value="<?php echo CHtml::encode($id); ?>"><?php echo CHtml::encode($className); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (Yii::app()->user->isAdmin()): ?>
            <!-- Teacher Selector (On Behalf Of) - Only for Admin -->
            <div class="mb-6">
                <label for="teacher-selector" class="block text-sm font-medium text-gray-700 mb-2">On Behalf Of</label>
                <select id="teacher-selector" name="teacher_id" disabled
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select a class to get teachers</option>
                </select>
            </div>
            <?php endif; ?>

            <!-- Loading Indicator -->
            <div id="loading-indicator" class="flex justify-center py-8 hidden">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
            </div>

            <!-- No Class Selected Message -->
            <div id="no-class-message" class="py-8 text-center text-gray-500">
                Please select a class to view students
            </div>

            <!-- Students Table -->
            <div id="students-table-container" class="hidden">
                <form id="attendance-form">
                    <input type="hidden" id="selected-date" name="date">
                    <input type="hidden" id="selected-class-id" name="class_id">
                    <?php if (Yii::app()->user->isAdmin()): ?>
                    <input type="hidden" id="selected-teacher-id" name="teacher_id">
                    <?php endif; ?>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="students-list" class="bg-white divide-y divide-gray-200">
                                <!-- Student rows will be inserted here dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <div>
                            <button type="button" id="mark-all-present" class="px-4 py-2 bg-green-100 text-green-800 rounded-md mr-2">Mark All Present</button>
                            <button type="button" id="mark-all-absent" class="px-4 py-2 bg-red-100 text-red-800 rounded-md">Mark All Absent</button>
                        </div>
                        <button type="submit" id="save-attendance" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            Save Attendance
                        </button>
                    </div>
                </form>
            </div>

            <!-- Result Message -->
            <div id="result-message" class="mt-4 p-4 rounded-md hidden"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classSelector = document.getElementById('class-selector');
        const attendanceDate = document.getElementById('attendance-date');
        const noClassMessage = document.getElementById('no-class-message');
        const studentsTableContainer = document.getElementById('students-table-container');
        const studentsList = document.getElementById('students-list');
        const loadingIndicator = document.getElementById('loading-indicator');
        const resultMessage = document.getElementById('result-message');
        const attendanceForm = document.getElementById('attendance-form');
        const markAllPresentBtn = document.getElementById('mark-all-present');
        const markAllAbsentBtn = document.getElementById('mark-all-absent');
        const saveAttendanceBtn = document.getElementById('save-attendance');
        const teacherSelector = document.getElementById('teacher-selector');

        // Update hidden fields when the date, class, or teacher changes
        function updateHiddenFields() {
            document.getElementById('selected-date').value = attendanceDate.value;
            document.getElementById('selected-class-id').value = classSelector.value;
            <?php if (Yii::app()->user->isAdmin()): ?>
            if (document.getElementById('selected-teacher-id')) {
                document.getElementById('selected-teacher-id').value = teacherSelector ? teacherSelector.value : '';
            }
            <?php endif; ?>
        }

        // Load teachers for the selected class (Admin only)
        function loadTeachers(classId) {
            <?php if (Yii::app()->user->isAdmin()): ?>
            if (!classId || !teacherSelector) return;

            // Reset and disable teacher selector
            teacherSelector.innerHTML = '<option value="">Loading teachers...</option>';
            teacherSelector.disabled = true;

            // Fetch teachers for the selected class
            fetch(`<?php echo Yii::app()->createUrl('teacher/getTeachersOfAClass'); ?>?classId=${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    teacherSelector.innerHTML = '<option value="">-- Select a Teacher --</option>';
                    
                    if (data.success && data.teachers && data.teachers.length > 0) {
                        data.teachers.forEach(teacher => {
                            const option = document.createElement('option');
                            option.value = teacher._id.$oid;
                            option.textContent = teacher.name || 'Unknown Teacher';
                            teacherSelector.appendChild(option);
                        });
                        teacherSelector.disabled = false;
                    } else {
                        teacherSelector.innerHTML = '<option value="">No teachers found for this class</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading teachers:', error);
                    teacherSelector.innerHTML = '<option value="">Error loading teachers</option>';
                });
            <?php endif; ?>
        }

        // Load students for the selected class
        function loadStudents() {
            const classId = classSelector.value;

            if (!classId) {
                noClassMessage.classList.remove('hidden');
                studentsTableContainer.classList.add('hidden');
                <?php if (Yii::app()->user->isAdmin()): ?>
                // Reset teacher selector
                if (teacherSelector) {
                    teacherSelector.innerHTML = '<option value="">Select a class to get teachers</option>';
                    teacherSelector.disabled = true;
                }
                <?php endif; ?>
                return;
            }

            // Load teachers for admin users
            loadTeachers(classId);

            // Show loading indicator
            noClassMessage.classList.add('hidden');
            studentsTableContainer.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');

            // Make AJAX request to get students
            fetch(`<?php echo Yii::app()->createUrl('student/getAllStudentsOfClass'); ?>?classId=${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading indicator
                    loadingIndicator.classList.add('hidden');

                    if (data.success && data.students && data.students.length > 0) {
                        // Render student rows
                        renderStudentRows(data.students);
                        studentsTableContainer.classList.remove('hidden');
                        updateHiddenFields();
                    } else {
                        // Show no students message
                        noClassMessage.textContent = 'No students found in this class';
                        noClassMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    loadingIndicator.classList.add('hidden');
                    noClassMessage.textContent = 'Error loading students. Please try again.';
                    noClassMessage.classList.remove('hidden');
                });
        }

        // Render student rows in the table
        function renderStudentRows(students) {
            studentsList.innerHTML = '';

            students.forEach(student => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${student.roll_no || 'N/A'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${student.user.name}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="attendance[${student._id.$oid}]" value="present" class="form-radio h-4 w-4 text-green-600">
                            <span class="ml-2 text-sm text-green-700">Present</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="attendance[${student._id.$oid}]" value="absent" class="form-radio h-4 w-4 text-red-600">
                            <span class="ml-2 text-sm text-red-700">Absent</span>
                        </label>
                    </div>
                </td>
            `;
                studentsList.appendChild(row);
            });

            // Enable the save button
            saveAttendanceBtn.disabled = false;
        }

        // Mark all Present:
        markAllPresentBtn.addEventListener('click', () => {
            // For each group of radios per student:
            const radioGroups = new Set();
            document.querySelectorAll('input[type="radio"][name^="attendance"]').forEach(radio => {
                radioGroups.add(radio.name);
            });
            radioGroups.forEach(name => {
                const presentRadio = document.querySelector(`input[name="${name}"][value="present"]`);
                if (presentRadio) presentRadio.checked = true;
            });
        });

        // Mark all Absent:
        markAllAbsentBtn.addEventListener('click', () => {
            const radioGroups = new Set();
            document.querySelectorAll('input[type="radio"][name^="attendance"]').forEach(radio => {
                radioGroups.add(radio.name);
            });
            radioGroups.forEach(name => {
                const absentRadio = document.querySelector(`input[name="${name}"][value="absent"]`);
                if (absentRadio) absentRadio.checked = true;
            });
        });


        // When class selection changes, load students and teachers
        classSelector.addEventListener('change', loadStudents);

        // When date changes, update hidden field
        attendanceDate.addEventListener('change', updateHiddenFields);

        <?php if (Yii::app()->user->isAdmin()): ?>
        // When teacher selection changes, update hidden field
        if (teacherSelector) {
            teacherSelector.addEventListener('change', updateHiddenFields);
        }
        <?php endif; ?>
        
        // Form submission
        attendanceForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate that all students have attendance marked
            const studentsWithoutAttendance = [];
            const radioGroups = new Set();

            document.querySelectorAll('input[type="radio"][name^="attendance"]').forEach(radio => {
                radioGroups.add(radio.name);
            });

            radioGroups.forEach(name => {
                if (!document.querySelector(`input[name="${name}"]:checked`)) {
                    studentsWithoutAttendance.push(name);
                }
            });

            if (studentsWithoutAttendance.length > 0) {
                showMessage('Please mark attendance status for all students', 'error');
                return;
            }

            // Disable save button and show loading
            saveAttendanceBtn.disabled = true;
            loadingIndicator.classList.remove('hidden');

            // Prepare form data
            const formData = new FormData();
            formData.append('date', attendanceDate.value);
            formData.append('class_id', classSelector.value);

            <?php if (Yii::app()->user->isAdmin()): ?>
            // Add teacher ID for admin users
            if (teacherSelector && teacherSelector.value) {
                formData.append('teacher_id', teacherSelector.value);
            }
            <?php endif; ?>

            // Collect all present student IDs
            const presentStudentIds = [];
            radioGroups.forEach(name => {
                const presentRadio = document.querySelector(`input[name="${name}"][value="present"]:checked`);
                if (presentRadio) {
                    // name is like attendance[<student_id>], extract student_id
                    const studentId = name.match(/^attendance\[(.+)\]$/)[1];
                    presentStudentIds.push(studentId);
                }
            });

            // Append present student IDs array to formData as 'student_ids[]'
            presentStudentIds.forEach(id => {
                formData.append('student_ids[]', id);
            });

            // Submit form via AJAX
            fetch('<?php echo Yii::app()->createUrl("attendance/save"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.classList.add('hidden');

                    if (data.success) {
                        showMessage('Attendance saved successfully!', 'success');
                    } else {
                        showMessage(data.message || 'Error saving attendance', 'error');
                        saveAttendanceBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error saving attendance:', error);
                    loadingIndicator.classList.add('hidden');
                    showMessage('An error occurred while saving attendance', 'error');
                    saveAttendanceBtn.disabled = false;
                });
        });

        // Show result message
        function showMessage(message, type) {
            resultMessage.textContent = message;
            resultMessage.className = 'mt-4 p-4 rounded-md';

            if (type === 'success') {
                resultMessage.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
            } else {
                resultMessage.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
            }

            resultMessage.classList.remove('hidden');

            // Auto-hide after 5 seconds
            setTimeout(() => {
                resultMessage.classList.add('hidden');
            }, 5000);
        }
    });
</script>