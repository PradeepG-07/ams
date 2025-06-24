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
                    <input type="date" id="attendance-date" name="attendance_date" 
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
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
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
    
    // Update hidden fields when the date or class changes
    function updateHiddenFields() {
        document.getElementById('selected-date').value = attendanceDate.value;
        document.getElementById('selected-class-id').value = classSelector.value;
    }
    
    // Load students for the selected class
    function loadStudents() {
        const classId = classSelector.value;
        
        if (!classId) {
            noClassMessage.classList.remove('hidden');
            studentsTableContainer.classList.add('hidden');
            return;
        }
        
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
                    ${student.name}
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
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text" name="notes[${student._id}]" placeholder="Optional notes" 
                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </td>
            `;
            studentsList.appendChild(row);
        });
        
        // Enable the save button
        saveAttendanceBtn.disabled = false;
    }
    
    // Mark all students as present
    markAllPresentBtn.addEventListener('click', function() {
        document.querySelectorAll('input[value="present"]').forEach(radio => {
            radio.checked = true;
        });
    });
    
    // Mark all students as absent
    markAllAbsentBtn.addEventListener('click', function() {
        document.querySelectorAll('input[value="absent"]').forEach(radio => {
            radio.checked = true;
        });
    });
    
    // When class selection changes, load students
    classSelector.addEventListener('change', loadStudents);
    
    // When date changes, update hidden field
    attendanceDate.addEventListener('change', updateHiddenFields);
    
    // Form submission
    attendanceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate that all students have attendance marked
        const unmarkedStudents = document.querySelectorAll('input[type="radio"]:not(:checked)');
        if (unmarkedStudents.length > 0) {
            showMessage('Please mark attendance status for all students', 'error');
            return;
        }
        
        // Disable save button and show loading
        saveAttendanceBtn.disabled = true;
        loadingIndicator.classList.remove('hidden');
        
        // Prepare form data
        const formData = new FormData(attendanceForm);
        
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