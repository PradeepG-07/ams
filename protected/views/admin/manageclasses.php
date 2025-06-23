<?php
/* @var $this AdminController */
/* @var $classrooms array */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - AMS Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-green-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Class Management</h1>
                        <p class="text-green-100">Attendance Management System</p>
                    </div>
                    <div>
                        <button id="addClassBtn" class="bg-white text-green-600 px-4 py-2 rounded-md font-medium hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Class
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="w-full md:w-1/3">
                        <div class="relative">
                            <input id="searchInput" type="text" placeholder="Search classes..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="academicYearFilter" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                            <select id="academicYearFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">All Years</option>
                                <option value="2022-2023">2022-2023</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2024-2025">2024-2025</option>
                            </select>
                        </div>
                        
                        <button id="exportBtn" class="mt-6 flex items-center text-sm bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Classes Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700">Class List</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Class Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Academic Year
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Teacher
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Students
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($classrooms)): ?>
                                <?php foreach ($classrooms as $classroom): ?>
                                <tr class="hover:bg-gray-50" data-id="<?php echo (string)$classroom->_id; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <span class="text-green-800 font-semibold">
                                                        <?php echo strtoupper(substr($classroom->class_name ?? '', 0, 2)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo CHtml::encode($classroom->class_name ?? 'N/A'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo CHtml::encode($classroom->subject ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo CHtml::encode($classroom->academic_year ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $teacherName = 'Not Assigned';
                                            if (!empty($classroom->class_teacher_id)) {
                                                $teacher = Teacher::model()->findByPk(new MongoDB\BSON\ObjectId($classroom->class_teacher_id));
                                                if ($teacher) {
                                                    $teacherName = $teacher->first_name . ' ' . $teacher->last_name;
                                                }
                                            }
                                        ?>
                                        <div class="text-sm text-gray-900"><?php echo CHtml::encode($teacherName); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded-full text-center w-12">
                                            <?php echo count($classroom->students ?? []); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button data-id="<?php echo (string)$classroom->_id; ?>" class="view-class text-green-600 hover:text-green-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button data-id="<?php echo (string)$classroom->_id; ?>" class="edit-class text-indigo-600 hover:text-indigo-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button data-id="<?php echo (string)$classroom->_id; ?>" class="delete-class text-red-600 hover:text-red-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No classes found. Add classes using the button above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    <p class="text-sm text-gray-700">
                        Total Classes: <span class="font-medium"><?php echo count($classrooms ?? []); ?></span>
                    </p>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Add/Edit Class Modal -->
    <div id="classModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800">Add New Class</h2>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="formFeedback" class="mb-4 hidden">
                <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 hidden"></div>
                <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 hidden"></div>
            </div>
            
            <form id="classForm">
                <input type="hidden" id="classId" name="ClassRoom[_id]" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="className" class="block text-sm font-medium text-gray-700 mb-1">Class Name</label>
                        <input type="text" id="className" name="ClassRoom[class_name]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" id="subject" name="ClassRoom[subject]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                    <div>
                        <label for="academicYear" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                        <select id="academicYear" name="ClassRoom[academic_year]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">Select Academic Year</option>
                            <option value="2022-2023">2022-2023</option>
                            <option value="2023-2024">2023-2024</option>
                            <option value="2024-2025">2024-2025</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2 border-b pb-1">Assign Teacher</h3>
                    <div class="flex items-center">
                        <div class="relative w-full">
                            <select id="teacherDropdown" name="ClassRoom[class_teacher_id]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Teacher</option>
                                <!-- Teacher options will be loaded via JavaScript -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2 border-b pb-1">Add Students</h3>
                    <div id="studentAssignment" class="hidden"> <!-- Only show after class is created -->
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="relative w-full">
                                <select id="studentDropdown" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select a Student to Add</option>
                                    <!-- Student options will be loaded via JavaScript -->
                                </select>
                            </div>
                            <button type="button" id="addStudentBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Add
                            </button>
                        </div>
                    </div>
                    
                    <div id="noStudentsMessage" class="text-gray-500 text-sm">Students can be added after saving the class.</div>
                    
                    <div id="assignedStudents" class="mt-4 hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Assigned Students:</h4>
                        <div id="studentsList" class="space-y-2">
                            <!-- Assigned students will be displayed here -->
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" id="cancelForm" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="saveButton" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <span>Save Class</span>
                        <span id="saveSpinner" class="hidden ml-2 animate-spin">&#8635;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- View Class Modal -->
    <div id="viewClassModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="viewModalTitle" class="text-xl font-bold text-gray-800">Class Details</h2>
                <button id="closeViewModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="classDetails" class="mb-6">
                <div class="flex items-center mb-4">
                    <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                        <span id="classInitials" class="text-green-800 text-2xl font-semibold"></span>
                    </div>
                    <div class="ml-4">
                        <h3 id="viewClassName" class="text-lg font-semibold text-gray-800"></h3>
                        <p id="viewSubject" class="text-gray-600"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Academic Year</h4>
                        <p id="viewAcademicYear" class="font-medium"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Teacher</h4>
                        <p id="viewTeacher" class="font-medium"></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Enrolled Students</h3>
                    <div id="viewStudents" class="bg-gray-50 p-4 rounded-md">
                        <div id="studentCountBadge" class="mb-3 inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full"></div>
                        <div id="viewStudentsList" class="space-y-2">
                            <!-- Student list will be displayed here -->
                        </div>
                        <div id="noEnrolledStudents" class="text-gray-500 text-sm hidden">No students enrolled in this class.</div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button id="closeViewBtn" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Close
                </button>
                <button id="editFromView" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Edit
                </button>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h2>
                <p class="text-gray-600">Are you sure you want to delete this class? This action cannot be undone and will remove all student and teacher associations.</p>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmDelete" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM elements
            const classModal = document.getElementById('classModal');
            const viewClassModal = document.getElementById('viewClassModal');
            const deleteModal = document.getElementById('deleteModal');
            const classForm = document.getElementById('classForm');
            const formFeedback = document.getElementById('formFeedback');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const saveButton = document.getElementById('saveButton');
            const saveSpinner = document.getElementById('saveSpinner');
            
            // Class management state
            let classStudents = {}; // For storing students in the current class being edited
            
            // Helper functions
            function resetFormMessages() {
                formFeedback.classList.add('hidden');
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');
                successMessage.textContent = '';
                errorMessage.textContent = '';
            }
            
            function displayFormMessage(isSuccess, message) {
                formFeedback.classList.remove('hidden');
                
                if (isSuccess) {
                    successMessage.innerHTML = message;
                    successMessage.classList.remove('hidden');
                    errorMessage.classList.add('hidden');
                } else {
                    errorMessage.innerHTML = message;
                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');
                }
            }
            
            // Load teachers for the dropdown
            function loadTeachers(currentTeacherId = '') {
                const teacherDropdown = document.getElementById('teacherDropdown');
                
                // Add the current teacher ID as a URL parameter if editing
                let url = '<?php echo Yii::app()->createUrl("admin/GetFreeTeachers"); ?>';
                if (currentTeacherId) {
                    url += `?includeTeacherId=${currentTeacherId}`;
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        teacherDropdown.innerHTML = '<option value="">Select Teacher</option>';
                        
                        if (data.success && data.teachers) {
                            data.teachers.forEach(teacher => {
                                const option = document.createElement('option');
                                option.value = teacher._id;
                                option.textContent = `${teacher.first_name} ${teacher.last_name}`;
                                teacherDropdown.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading teachers:', error);
                    });
            }
            
            // Load students for the dropdown
            function loadStudents() {
                const studentDropdown = document.getElementById('studentDropdown');
                
                fetch('<?php echo Yii::app()->createUrl("admin/getStudents"); ?>')
                    .then(response => response.json())
                    .then(data => {
                        studentDropdown.innerHTML = '<option value="">Select a Student to Add</option>';
                        
                        if (data.success && data.students) {
                            data.students.forEach(student => {
                                // Only add students that are not already in the class
                                if (!classStudents[student._id]) {
                                    const option = document.createElement('option');
                                    option.value = student._id;
                                    option.textContent = `${student.first_name} ${student.last_name} (${student.roll_no})`;
                                    studentDropdown.appendChild(option);
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading students:', error);
                    });
            }
            
            // Add a student to the class UI
            function addStudentToUI(studentId, studentName, rollNo) {
                const studentsList = document.getElementById('studentsList');
                const noStudentsMessage = document.getElementById('noStudentsMessage');
                const assignedStudents = document.getElementById('assignedStudents');
                
                // Hide the "no students" message
                noStudentsMessage.classList.add('hidden');
                assignedStudents.classList.remove('hidden');
                
                // Create the student item
                const studentItem = document.createElement('div');
                studentItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-md';
                studentItem.dataset.studentId = studentId;
                
                studentItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-800 font-medium text-sm">${studentName.split(' ').map(n => n[0]).join('')}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium">${studentName}</p>
                            <p class="text-xs text-gray-500">Roll No: ${rollNo}</p>
                        </div>
                    </div>
                    <button type="button" class="remove-student-btn text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                
                // Add click handler for remove button
                studentItem.querySelector('.remove-student-btn').addEventListener('click', function() {
                    removeStudentFromClass(studentId);
                    studentItem.remove();
                    
                    // If no students left, show the message
                    if (studentsList.children.length === 0) {
                        noStudentsMessage.classList.remove('hidden');
                        assignedStudents.classList.add('hidden');
                    }
                    
                    // Refresh dropdown to include this student again
                    loadStudents();
                });
                
                studentsList.appendChild(studentItem);
            }
            
            // Initialize a class form for adding a new class
            function initNewClassForm() {
                document.getElementById('modalTitle').textContent = 'Add New Class';
                document.getElementById('classId').value = '';
                classForm.reset();
                
                // Hide student assignment until class is created
                document.getElementById('studentAssignment').classList.add('hidden');
                document.getElementById('noStudentsMessage').classList.remove('hidden');
                document.getElementById('assignedStudents').classList.add('hidden');
                document.getElementById('studentsList').innerHTML = '';
                
                classStudents = {}; // Reset student list
                loadTeachers(); // Call without parameters for new class
                
                classModal.classList.remove('hidden');
            }
            
            // Populate form with existing class data
            function populateClassForm(classroom) {
                if (!classroom) return;
                
                // Debug the incoming classroom._id to see its structure
                console.log("Raw classroom object:", classroom);
                console.log("Raw classroom._id:", classroom._id);
                
                // Instead of using the object directly, we need to ensure we use the string value
                // MongoDB ObjectIds often have a string representation accessible via toString() or $id property
                let classIdStr = '';
                if (classroom._id) {
                    // Try different approaches to get the string representation
                    if (typeof classroom._id === 'string') {
                        classIdStr = classroom._id;
                    } else if (classroom._id.$id) {
                        // Some MongoDB drivers use $id for the string representation
                        classIdStr = classroom._id.$id;
                    } else if (typeof classroom._id === 'object') {
                        // The server might have already stringified it in a property
                        classIdStr = classroom._id.toString ? classroom._id.toString() : String(classroom._id);
                    }
                }
                
                console.log("Extracted classIdStr:", classIdStr);
                document.getElementById('classId').value = classIdStr;
                
                document.getElementById('className').value = classroom.class_name || '';
                document.getElementById('subject').value = classroom.subject || '';
                document.getElementById('academicYear').value = classroom.academic_year || '';
                
                // Load teachers including the current teacher
                if (classroom.class_teacher_id) {
                    loadTeachers(classroom.class_teacher_id);
                } else {
                    loadTeachers();
                }
                
                // Set the teacher if one is assigned (do this after teachers are loaded)
                const teacherDropdown = document.getElementById('teacherDropdown');
                if (classroom.class_teacher_id) {
                    // We need to set this with a slight delay to ensure the dropdown is populated
                    setTimeout(() => {
                        teacherDropdown.value = classroom.class_teacher_id;
                    }, 500);
                } else {
                    teacherDropdown.value = '';
                }
                
                // Show student assignment section for existing classes
                document.getElementById('studentAssignment').classList.remove('hidden');
                document.getElementById('noStudentsMessage').classList.add('hidden');
                
                // Reset and populate student list
                document.getElementById('studentsList').innerHTML = '';
                classStudents = {};
                
                // Add existing students to the UI
                if (classroom.students && Object.keys(classroom.students).length > 0) {
                    document.getElementById('assignedStudents').classList.remove('hidden');
                    
                    for (const studentId in classroom.students) {
                        const student = classroom.students[studentId];
                        classStudents[studentId] = student;
                        addStudentToUI(studentId, student.name, student.roll_no);
                    }
                } else {
                    document.getElementById('assignedStudents').classList.add('hidden');
                }
                
                loadStudents();
            }
            
            // Function to send request to add a student to a class
            function addStudentToClass(classId, studentId) {
                if (!classId || !studentId) {
                    displayFormMessage(false, 'Missing class or student ID');
                    return Promise.reject('Missing IDs');
                }
                
                const formData = new FormData();
                formData.append('classroomId', classId);
                formData.append('studentId', studentId);
                
                return fetch('<?php echo Yii::app()->createUrl("admin/addStudentToClass"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to add student');
                    }
                    return data;
                });
            }
            
            // Function to send request to remove a student from a class
            function removeStudentFromClass(studentId) {
                const classId = document.getElementById('classId').value;
                if (!classId || !studentId) return;
                
                // For new unsaved classes, just update the UI
                if (classId === '') {
                    delete classStudents[studentId];
                    return;
                }
                
                const formData = new FormData();
                formData.append('classroomId', classId);
                formData.append('studentId', studentId);
                
                fetch('<?php echo Yii::app()->createUrl("admin/removeStudentFromClass"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        delete classStudents[studentId];
                        displayFormMessage(true, 'Student removed from class');
                    } else {
                        displayFormMessage(false, data.message || 'Failed to remove student');
                    }
                })
                .catch(error => {
                    console.error('Error removing student:', error);
                    displayFormMessage(false, 'Error removing student');
                });
            }
            
            // Populate the view modal with class details
            function populateClassView(classroom) {
                if (!classroom) return;
                
                // Set class initials and basic info
                document.getElementById('classInitials').textContent = classroom.class_name.substring(0, 2).toUpperCase();
                // Ensure we're storing the ID as a string
                document.getElementById('classInitials').setAttribute('data-id', String(classroom._id));
                document.getElementById('viewClassName').textContent = classroom.class_name;
                document.getElementById('viewSubject').textContent = classroom.subject;
                document.getElementById('viewAcademicYear').textContent = classroom.academic_year || 'N/A';
                
                // Teacher info
                let teacherName = 'Not Assigned';
                if (classroom.teacherName) {
                    teacherName = classroom.teacherName;
                }
                document.getElementById('viewTeacher').textContent = teacherName;
                
                // Student info
                const studentCountBadge = document.getElementById('studentCountBadge');
                const viewStudentsList = document.getElementById('viewStudentsList');
                const noEnrolledStudents = document.getElementById('noEnrolledStudents');
                
                viewStudentsList.innerHTML = '';
                
                const studentCount = classroom.students ? Object.keys(classroom.students).length : 0;
                studentCountBadge.textContent = `${studentCount} Student${studentCount !== 1 ? 's' : ''}`;
                
                if (studentCount > 0) {
                    noEnrolledStudents.classList.add('hidden');
                    viewStudentsList.classList.remove('hidden');
                    
                    for (const studentId in classroom.students) {
                        const student = classroom.students[studentId];
                        const studentItem = document.createElement('div');
                        studentItem.className = 'flex items-center bg-white p-2 rounded-md';
                        
                        studentItem.innerHTML = `
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-800 font-medium text-sm">${student.name.split(' ').map(n => n[0]).join('')}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium">${student.name}</p>
                                <p class="text-xs text-gray-500">Roll No: ${student.roll_no}</p>
                            </div>
                        `;
                        
                        viewStudentsList.appendChild(studentItem);
                    }
                } else {
                    noEnrolledStudents.classList.remove('hidden');
                    viewStudentsList.classList.add('hidden');
                }
            }
            
            // Add event listeners
            document.getElementById('addClassBtn').addEventListener('click', initNewClassForm);
            
            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');
                
                tableRows.forEach(row => {
                    if (row.cells.length === 1) return; // Skip "No classes found" row
                    
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                });
            });
            
            // Academic year filter
            document.getElementById('academicYearFilter').addEventListener('change', function() {
                const filterValue = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');
                
                if (!filterValue) {
                    tableRows.forEach(row => {
                        row.style.display = '';
                    });
                    return;
                }
                
                tableRows.forEach(row => {
                    if (row.cells.length === 1) return; // Skip "No classes found" row
                    
                    const academicYearCell = row.cells[2].textContent.toLowerCase();
                    row.style.display = academicYearCell.includes(filterValue) ? '' : 'none';
                });
            });
            
            // View class details
            document.querySelectorAll('.view-class').forEach(button => {
                button.addEventListener('click', function() {
                    const classId = this.getAttribute('data-id');
                    
                    fetch(`<?php echo Yii::app()->createUrl("admin/getClassroom"); ?>?id=${classId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                populateClassView(data.classroom);
                                viewClassModal.classList.remove('hidden');
                            } else {
                                alert(data.message || 'Failed to load class data');
                            }
                        })
                        .catch(error => {
                            console.error('Error loading class data:', error);
                            alert('eheh');
                        });
                });
            });
            
            // Edit class
            document.querySelectorAll('.edit-class').forEach(button => {
                button.addEventListener('click', function() {
                    resetFormMessages();
                    const classId = this.getAttribute('data-id');
                    document.getElementById('modalTitle').textContent = 'Edit Class';
                    
                    saveButton.disabled = true;
                    saveSpinner.classList.remove('hidden');
                    
                    // Ensure classId is a string here too
                    fetch(`<?php echo Yii::app()->createUrl("admin/getClassroom"); ?>?id=${String(classId)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                populateClassForm(data.classroom);
                                classModal.classList.remove('hidden');
                            } else {
                                displayFormMessage(false, data.message || 'Failed to load class data');
                            }
                            saveButton.disabled = false;
                            saveSpinner.classList.add('hidden');
                        })
                        .catch(error => {
                            console.error('Error loading class data:', error);
                            displayFormMessage(false, 'An error occurred while loading class data');
                            saveButton.disabled = false;
                            saveSpinner.classList.add('hidden');
                        });
                });
            });
            
            // Delete class
            let classIdToDelete = null;
            
            document.querySelectorAll('.delete-class').forEach(button => {
                button.addEventListener('click', function() {
                    classIdToDelete = this.getAttribute('data-id');
                    deleteModal.classList.remove('hidden');
                });
            });
            
            document.getElementById('confirmDelete').addEventListener('click', function() {
                if (!classIdToDelete) return;
                
                this.disabled = true;
                this.textContent = 'Deleting...';
                
                fetch(`<?php echo Yii::app()->createUrl("admin/deleteClassroom"); ?>?id=${classIdToDelete}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete class');
                        this.disabled = false;
                        this.textContent = 'Delete';
                    }
                })
                .catch(error => {
                    console.error('Error deleting class:', error);
                    alert('An error occurred while deleting the class');
                    this.disabled = false;
                    this.textContent = 'Delete';
                })
                .finally(() => {
                    deleteModal.classList.add('hidden');
                });
            });
            
            // Form submission
            classForm.addEventListener('submit', function(e) {
                e.preventDefault();
                resetFormMessages();
                
                // Get the classId and properly debug it
                const classIdInput = document.getElementById('classId');
                console.log("Hidden input field:", classIdInput);
                console.log("Hidden input name:", classIdInput.name);
                console.log("Hidden input value:", classIdInput.value);
                
                const classId = classIdInput.value;
                
                // Enhanced debugging for classId
                console.log("Raw classId value:", classId);
                console.log("classId type:", typeof classId);
                console.log("String conversion:", String(classId));
                
                const formData = new FormData(this);
                const isNewClass = !classId;
                
                // Log form data for debugging
                console.log("Form data entries:");
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                saveButton.disabled = true;
                saveSpinner.classList.remove('hidden');
                
                // Add additional validation to ensure classId is a string
                const url = isNewClass 
                    ? '<?php echo Yii::app()->createUrl("admin/createClassroom"); ?>'
                    : `<?php echo Yii::app()->createUrl("admin/updateClassroom"); ?>?id=${String(classId)}`;
                
                console.log("Full submission URL:", url);
                
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.text().then(text => {
                        try {
                            console.log("Raw response:", text);
                            return JSON.parse(text);
                        } catch (e) {
                            console.error("JSON parse error:", e);
                            console.log("Failed to parse response as JSON:", text);
                            throw new Error("Invalid JSON response");
                        }
                    });
                })
                .then(data => {
                    console.log("Parsed response data:", data);
                    if (data.success) {
                        if (isNewClass) {
                            // Show student assignment section for newly created class
                            document.getElementById('classId').value = data.classroomId;
                            document.getElementById('studentAssignment').classList.remove('hidden');
                            document.getElementById('noStudentsMessage').classList.add('hidden');
                            displayFormMessage(true, 'Class created successfully! You can now add students.');
                            // Load students for assignment
                            loadStudents();
                        } else {
                            displayFormMessage(true, 'Class updated successfully!');
                            // Reload the page after a delay
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    } else {
                        let errorMsg = data.message || 'An error occurred.';
                        if (data.errors) {
                            errorMsg += '<ul class="list-disc pl-5 mt-2">';
                            for (const field in data.errors) {
                                for (const error of data.errors[field]) {
                                    errorMsg += `<li>${error}</li>`;
                                }
                            }
                            errorMsg += '</ul>';
                        }
                        displayFormMessage(false, errorMsg);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    displayFormMessage(false, 'A network error occurred. Please try again.');
                })
                .finally(() => {
                    saveButton.disabled = false;
                    saveSpinner.classList.add('hidden');
                });
            });
            
            // Add student button
            document.getElementById('addStudentBtn').addEventListener('click', function() {
                const studentDropdown = document.getElementById('studentDropdown');
                const selectedStudentId = studentDropdown.value;
                
                if (!selectedStudentId) {
                    alert('Please select a student to add');
                    return;
                }
                
                const classId = document.getElementById('classId').value;
                if (!classId) {
                    alert('Please save the class before adding students');
                    return;
                }
                
                // Get student details from the selected option
                const selectedOption = studentDropdown.options[studentDropdown.selectedIndex];
                const studentName = selectedOption.text.split(' (')[0];
                const rollNo = selectedOption.text.match(/\(([^)]+)\)/)[1];
                
                // Disable the button during the request
                this.disabled = true;
                
                // Send the request to add the student
                addStudentToClass(classId, selectedStudentId)
                    .then(data => {
                        // Add student to local state and UI
                        classStudents[selectedStudentId] = {
                            name: studentName,
                            roll_no: rollNo
                        };
                        
                        addStudentToUI(selectedStudentId, studentName, rollNo);
                        
                        // Reset dropdown
                        studentDropdown.value = '';
                        
                        // Reload student dropdown to remove the added student
                        loadStudents();
                        
                        displayFormMessage(true, 'Student added to class successfully');
                    })
                    .catch(error => {
                        console.error('Error adding student:', error);
                        displayFormMessage(false, 'Failed to add student to class');
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
            });
            
            // Close modals
            document.getElementById('closeModal').addEventListener('click', () => classModal.classList.add('hidden'));
            document.getElementById('cancelForm').addEventListener('click', () => classModal.classList.add('hidden'));
            document.getElementById('closeViewModal').addEventListener('click', () => viewClassModal.classList.add('hidden'));
            document.getElementById('closeViewBtn').addEventListener('click', () => viewClassModal.classList.add('hidden'));
            document.getElementById('cancelDelete').addEventListener('click', () => deleteModal.classList.add('hidden'));
            
            // Edit from view
            document.getElementById('editFromView').addEventListener('click', function() {
                const classId = document.getElementById('classInitials').getAttribute('data-id');
                viewClassModal.classList.add('hidden');
                
                // Ensure the ID is properly handled as a string
                const editButton = document.querySelector(`.edit-class[data-id="${String(classId)}"]`);
                if (editButton) {
                    editButton.click();
                }
            });
            
            // Export button
            document.getElementById('exportBtn').addEventListener('click', function() {
                alert('Export functionality would generate a CSV or Excel file with class data.');
            });
        });
    </script>
</body>
</html>
