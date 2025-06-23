<?php
/* @var $this AdminController */
/* @var $teachers array */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - AMS Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-purple-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Teacher Management</h1>
                        <p class="text-purple-100">Attendance Management System</p>
                    </div>
                    <div>
                        <button id="addTeacherBtn" class="bg-white text-purple-600 px-4 py-2 rounded-md font-medium hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-purple-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Teacher
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
                            <input id="searchInput" type="text" placeholder="Search teachers..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button id="exportBtn" class="flex items-center text-sm bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Teachers Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700">Teacher List</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Teacher
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Employee ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Classes Assigned
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr class="hover:bg-gray-50" data-id="<?php echo $teacher->_id; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                    <span class="text-purple-800 font-semibold">
                                                        <?php echo strtoupper(substr($teacher->first_name ?? '', 0, 1) . substr($teacher->last_name ?? '', 0, 1)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo CHtml::encode(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo CHtml::encode($teacher->EmployeeID ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo CHtml::encode($teacher->email ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <div class="flex flex-wrap gap-2">
                                                <?php if (!empty($teacher->classes) && is_array($teacher->classes)): ?>
                                                    <?php foreach (array_slice($teacher->classes, 0, 2) as $class): ?>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                                            <?php echo CHtml::encode($class['name'] . ' - ' . $class['subject']); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                    
                                                    <?php if (count($teacher->classes) > 2): ?>
                                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                            +<?php echo count($teacher->classes) - 2; ?> more
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-400">No classes</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button data-id="<?php echo $teacher->_id; ?>" class="view-teacher text-purple-600 hover:text-purple-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button data-id="<?php echo $teacher->_id; ?>" class="edit-teacher text-indigo-600 hover:text-indigo-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button data-id="<?php echo $teacher->_id; ?>" class="delete-teacher text-red-600 hover:text-red-900">
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
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No teachers found. Add teachers using the button above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    <p class="text-sm text-gray-700">
                        Total Teachers: <span class="font-medium"><?php echo count($teachers); ?></span>
                    </p>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Add/Edit Teacher Modal -->
    <div id="teacherModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-4xl max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800">Add New Teacher</h2>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="formFeedback" class="mb-4 hidden">
                <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 hidden"></div>
                <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 hidden"></div>
            </div>
            
            <form id="teacherForm">
                <input type="hidden" id="teacherId" name="teacherId" value="">
                
                <h3 class="text-lg font-semibold text-gray-700 mb-2 border-b pb-1">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="firstName" name="Teacher[first_name]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="lastName" name="Teacher[last_name]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="Teacher[email]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="employeeId" class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                        <input type="text" id="employeeId" name="Teacher[EmployeeID]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div id="passwordField">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="Teacher[password]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (for edit).</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 mb-2 mt-6 border-b pb-1">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="addressLine1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                        <input type="text" id="addressLine1" name="Address[address_line1]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="addressLine2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                        <input type="text" id="addressLine2" name="Address[address_line2]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" id="city" name="Address[city]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <input type="text" id="state" name="Address[state]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                        <input type="text" id="zip" name="Address[zip]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" id="country" name="Address[country]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 mb-2 mt-6 border-b pb-1">Qualifications</h3>
                <div id="qualificationsContainer" class="space-y-4 mb-4">
                    <!-- Qualification items will be added here by JS -->
                </div>
                <button type="button" id="addQualificationBtn" class="mb-4 text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                    Add Qualification
                </button>

                <h3 class="text-lg font-semibold text-gray-700 mb-2 mt-6 border-b pb-1">Class Assignment</h3>
                <div class="flex items-center space-x-2 mb-2">
                    <div class="relative w-full">
                        <select id="classroomDropdown" class="appearance-none w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Select a Class to Assign</option>
                        </select>
                         <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                    <button type="button" id="assignClassBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50">
                        Add Class
                    </button>
                </div>
                <div id="assignedClassesContainer" class="mt-2 mb-4">
                    <div id="noClassesMessage" class="text-gray-500 text-sm">No classes assigned</div>
                    <div id="assignedClassesList" class="flex flex-wrap gap-2">
                        <!-- Assigned classes will be added here -->
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 mt-8">
                    <button type="button" id="cancelForm" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="saveButton" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                        <span>Save Teacher</span>
                        <span id="saveSpinner" class="hidden ml-2 animate-spin">&#8635;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- View Teacher Modal -->
    <div id="viewTeacherModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="viewModalTitle" class="text-xl font-bold text-gray-800">Teacher Details</h2>
                <button id="closeViewModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="teacherDetails" class="mb-6">
                <div class="flex items-center mb-4">
                    <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center">
                        <span id="teacherInitials" class="text-purple-800 text-2xl font-semibold"></span>
                    </div>
                    <div class="ml-4">
                        <h3 id="teacherName" class="text-lg font-semibold text-gray-800"></h3>
                        <p id="teacherEmail" class="text-gray-600"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Employee ID</h4>
                        <p id="viewEmployeeId" class="font-medium"></p>
                    </div>
                     <div class="bg-gray-50 p-4 rounded-md md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Classes Assigned</h4>
                        <div id="viewClasses" class="flex flex-wrap gap-2 mt-1"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Address Information</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p id="viewAddress" class="text-gray-700"></p>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Qualifications</h3>
                    <div id="viewQualifications" class="bg-gray-50 p-4 rounded-md space-y-2">
                        <!-- Qualifications displayed here -->
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button id="closeViewBtn" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Close</button>
                <!-- <button id="editFromView" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">Edit</button> -->
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h2>
                <p class="text-gray-600">Are you sure you want to delete this teacher? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end space-x-2">
                <button id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                <button id="confirmDelete" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const teacherModal = document.getElementById('teacherModal');
        const viewTeacherModal = document.getElementById('viewTeacherModal');
        const deleteModal = document.getElementById('deleteModal');
        const teacherForm = document.getElementById('teacherForm');
        const formFeedback = document.getElementById('formFeedback');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const saveButton = document.getElementById('saveButton');
        const saveSpinner = document.getElementById('saveSpinner');
        
        const qualificationsContainer = document.getElementById('qualificationsContainer');
        const addQualificationBtn = document.getElementById('addQualificationBtn');
        let qualificationIndex = 0;

        // Classroom assignment elements
        const classroomDropdown = document.getElementById('classroomDropdown');
        const assignClassBtn = document.getElementById('assignClassBtn');
        const assignedClassesList = document.getElementById('assignedClassesList');
        const noClassesMessage = document.getElementById('noClassesMessage');
        let teacherAssignedClasses = {}; // Store { className: {id, subject, academic_year} }

        function resetFormMessages() {
            formFeedback.classList.add('hidden');
            successMessage.classList.add('hidden'); errorMessage.classList.add('hidden');
            successMessage.textContent = ''; errorMessage.textContent = '';
        }

        function displayFormMessage(isSuccess, message) {
            formFeedback.classList.remove('hidden');
            if (isSuccess) {
                successMessage.innerHTML = message; successMessage.classList.remove('hidden'); errorMessage.classList.add('hidden');
            } else {
                errorMessage.innerHTML = message; errorMessage.classList.remove('hidden'); successMessage.classList.add('hidden');
            }
        }

        // Fix for qualification deletion
        function createQualificationItem(qual = {}, index) {
            const div = document.createElement('div');
            div.className = 'grid grid-cols-1 md:grid-cols-7 gap-2 border p-3 rounded-md relative qualification-item';
            
            // Store the qualification ID if it exists (for existing qualifications)
            if (qual._id) {
                div.dataset.qualId = qual._id;
            }
            
            div.innerHTML = `
                ${qual._id ? `<input type="hidden" name="Qualifications[${index}][_id]" value="${qual._id}">` : ''}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600">Degree</label>
                    <input type="text" name="Qualifications[${index}][degree]" value="${qual.degree || ''}" class="mt-1 w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-purple-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600">Field of Study</label>
                    <input type="text" name="Qualifications[${index}][field]" value="${qual.field || ''}" class="mt-1 w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Institute</label>
                    <input type="text" name="Qualifications[${index}][institute]" value="${qual.institute || ''}" class="mt-1 w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Year</label>
                    <input type="number" name="Qualifications[${index}][year]" value="${qual.year || ''}" class="mt-1 w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Grade</label>
                    <input type="text" name="Qualifications[${index}][grade]" value="${qual.grade || ''}" class="mt-1 w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-purple-500" required>
                </div>
                <button type="button" class="remove-qualification-btn absolute top-1 right-1 text-red-500 hover:text-red-700 md:relative md:top-auto md:right-auto md:self-end md:pb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            
            // Improved removal handling
            div.querySelector('.remove-qualification-btn').addEventListener('click', () => {
                // If this is an existing qualification (has an ID), mark it for deletion
                if (qual._id) {
                    // Create a hidden input to mark this qualification for deletion
                    const deletionMarker = document.createElement('input');
                    deletionMarker.type = 'hidden';
                    deletionMarker.name = 'DeleteQualifications[]';
                    deletionMarker.value = qual._id;
                    teacherForm.appendChild(deletionMarker);
                }
                
                // Remove the item from the DOM
                div.remove();
                
                // Show a temporary message confirming deletion
                displayFormMessage(true, 'Qualification removed. Save to apply changes.');
                setTimeout(() => {
                    resetFormMessages();
                }, 3000);
            });
            
            return div;
        }

        addQualificationBtn.addEventListener('click', () => {
            qualificationsContainer.appendChild(createQualificationItem({}, qualificationIndex));
            qualificationIndex++;
        });
        
        function populateTeacherForm(teacher) {
            if (!teacher) return;
            document.getElementById('firstName').value = teacher.first_name || '';
            document.getElementById('lastName').value = teacher.last_name || '';
            document.getElementById('email').value = teacher.email || '';
            document.getElementById('employeeId').value = teacher.EmployeeID || '';
            document.getElementById('password').value = ''; // Password field is not pre-filled for editing

            if (teacher.address) {
                document.getElementById('addressLine1').value = teacher.address.address_line1 || '';
                document.getElementById('addressLine2').value = teacher.address.address_line2 || '';
                document.getElementById('city').value = teacher.address.city || '';
                document.getElementById('state').value = teacher.address.state || '';
                document.getElementById('zip').value = teacher.address.zip || '';
                document.getElementById('country').value = teacher.address.country || '';
            } else {
                ['addressLine1', 'addressLine2', 'city', 'state', 'zip', 'country'].forEach(id => document.getElementById(id).value = '');
            }

            qualificationsContainer.innerHTML = '';
            qualificationIndex = 0;
            if (teacher.Qualifications && Array.isArray(teacher.Qualifications)) {
                teacher.Qualifications.forEach(qual => {
                    qualificationsContainer.appendChild(createQualificationItem(qual, qualificationIndex));
                    qualificationIndex++;
                });
            }
            
            // Populate assigned classes
            assignedClassesList.innerHTML = '';
            teacherAssignedClasses = {};
            noClassesMessage.style.display = 'block';

            if (teacher.classes && Object.keys(teacher.classes).length > 0) {
                noClassesMessage.style.display = 'none';
                for (const className in teacher.classes) {
                    if (teacher.classes.hasOwnProperty(className)) {
                        const classData = teacher.classes[className];
                        teacherAssignedClasses[className] = classData;
                        addAssignedClassToUI(className, classData.subject, classData.academic_year);
                    }
                }
            }
        }

        document.getElementById('addTeacherBtn').addEventListener('click', function() {
            resetFormMessages();
            document.getElementById('modalTitle').textContent = 'Add New Teacher';
            document.getElementById('teacherId').value = '';
            teacherForm.reset();
            qualificationsContainer.innerHTML = ''; qualificationIndex = 0;
            assignedClassesList.innerHTML = ''; teacherAssignedClasses = {}; noClassesMessage.style.display = 'block';
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('password').setAttribute('required', 'required');
            loadClassroomsForDropdown();
            teacherModal.classList.remove('hidden');
        });

        document.querySelectorAll('.edit-teacher').forEach(button => {
            button.addEventListener('click', function() {
                resetFormMessages();
                const id = this.getAttribute('data-id');
                document.getElementById('modalTitle').textContent = 'Edit Teacher';
                document.getElementById('teacherId').value = id;
                document.getElementById('passwordField').style.display = 'block';
                document.getElementById('password').removeAttribute('required');

                saveButton.disabled = true; saveSpinner.classList.remove('hidden');
                fetch(`<?php echo Yii::app()->createUrl('admin/getTeacher'); ?>?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            populateTeacherForm(data.teacher);
                            loadClassroomsForDropdown();
                            teacherModal.classList.remove('hidden');
                        } else {
                            displayFormMessage(false, data.message || 'Failed to load teacher data.');
                        }
                    })
                    .catch(error => displayFormMessage(false, 'Error loading teacher data: ' + error))
                    .finally(() => { saveButton.disabled = false; saveSpinner.classList.add('hidden'); });
            });
        });

        teacherForm.addEventListener('submit', function(e) {
            e.preventDefault();
            resetFormMessages();
            saveButton.disabled = true; saveSpinner.classList.remove('hidden');

            const id = document.getElementById('teacherId').value;
            const formData = new FormData(this);
            
            // Log form data for debugging
            console.log('Submitting qualification data:');
            for (const [key, value] of formData.entries()) {
                if (key.includes('Qualifications') || key.includes('DeleteQualifications')) {
                    console.log(key + ': ' + value);
                }
            }
            
            const url = id 
                ? `<?php echo Yii::app()->createUrl('admin/updateTeacher'); ?>?id=${id}` 
                : '<?php echo Yii::app()->createUrl('admin/createTeacher'); ?>';

            fetch(url, { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayFormMessage(true, data.message || (id ? 'Teacher updated successfully!' : 'Teacher created successfully!'));
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        let errorMsg = data.message || 'An error occurred.';
                        if (data.errors) {
                            errorMsg += '<ul class="list-disc pl-5 mt-2">';
                            for (const field in data.errors) {
                                data.errors[field].forEach(err => errorMsg += `<li>${err}</li>`);
                            }
                            errorMsg += '</ul>';
                        }
                        displayFormMessage(false, errorMsg);
                    }
                })
                .catch(error => displayFormMessage(false, 'Network error: ' + error))
                .finally(() => { saveButton.disabled = false; saveSpinner.classList.add('hidden'); });
        });

        // Delete Teacher
        let teacherIdToDelete = null;
        document.querySelectorAll('.delete-teacher').forEach(button => {
            button.addEventListener('click', function() {
                teacherIdToDelete = this.getAttribute('data-id');
                deleteModal.classList.remove('hidden');
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (!teacherIdToDelete) return;
            this.disabled = true; this.textContent = 'Deleting...';
            
            const formData = new FormData();
            formData.append('id', teacherIdToDelete);

            fetch(`<?php echo Yii::app()->createUrl('admin/deleteTeacher'); ?>`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to delete teacher.');
                }
            })
            .catch(() => alert('Error deleting teacher.'))
            .finally(() => {
                this.disabled = false; this.textContent = 'Delete';
                deleteModal.classList.add('hidden');
                teacherIdToDelete = null;
            });
        });
        
        // View Teacher
        document.querySelectorAll('.view-teacher').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`<?php echo Yii::app()->createUrl('admin/getTeacher'); ?>?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.teacher) {
                            const teacher = data.teacher;
                            document.getElementById('teacherInitials').textContent = ((teacher.first_name || '').charAt(0) + (teacher.last_name || '').charAt(0)).toUpperCase();
                            document.getElementById('teacherInitials').setAttribute('data-id', teacher._id);
                            document.getElementById('teacherName').textContent = `${teacher.first_name || ''} ${teacher.last_name || ''}`;
                            document.getElementById('teacherEmail').textContent = teacher.email || 'N/A';
                            document.getElementById('viewEmployeeId').textContent = teacher.EmployeeID || 'N/A';

                            const addressParts = [teacher.address?.address_line1, teacher.address?.address_line2, teacher.address?.city, teacher.address?.state, teacher.address?.zip, teacher.address?.country].filter(Boolean);
                            document.getElementById('viewAddress').textContent = addressParts.length > 0 ? addressParts.join(', ') : 'No address information.';

                            const qualContainer = document.getElementById('viewQualifications');
                            qualContainer.innerHTML = '';
                            if (teacher.Qualifications && teacher.Qualifications.length > 0) {
                                teacher.Qualifications.forEach(q => {
                                    const qDiv = document.createElement('div');
                                    qDiv.innerHTML = `<p class="text-sm"><span class="font-semibold">${q.degree || 'N/A'}</span> in ${q.field || 'N/A'}</p><p class="text-xs text-gray-600">${q.institute || 'N/A'} (${q.year || 'N/A'}) - Grade: ${q.grade || 'N/A'}</p>`;
                                    qualContainer.appendChild(qDiv);
                                });
                            } else {
                                qualContainer.textContent = 'No qualifications listed.';
                            }
                            
                            const classesContainer = document.getElementById('viewClasses');
                            classesContainer.innerHTML = '';
                            if (teacher.classes && Object.keys(teacher.classes).length > 0) {
                                Object.entries(teacher.classes).forEach(([name, details]) => {
                                    const classBadge = document.createElement('span');
                                    classBadge.className = 'px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800';
                                    classBadge.textContent = `${details.name} - ${details.subject || 'N/A'} (${details.academic_year || 'N/A'})`;
                                    classesContainer.appendChild(classBadge);
                                });
                            } else {
                                classesContainer.textContent = 'No classes assigned.';
                            }

                            viewTeacherModal.classList.remove('hidden');
                        } else {
                            alert(data.message || 'Failed to load teacher details.');
                        }
                    })
                    .catch(() => alert('Error loading teacher details.'));
            });
        });
        
        // Classroom assignment logic
        function loadClassroomsForDropdown() {
            fetch('<?php echo Yii::app()->createUrl('admin/getClassrooms'); ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        classroomDropdown.innerHTML = '<option value="">Select a Class to Assign</option>';
                        data.classrooms.forEach(classroom => {
                            if (!teacherAssignedClasses[classroom.name]) {
                                const option = document.createElement('option');
                                option.value = classroom.id;
                                option.textContent = `${classroom.name} - ${classroom.subject} (${classroom.academicYear})`;
                                option.dataset.name = classroom.name;
                                option.dataset.subject = classroom.subject;
                                option.dataset.academicYear = classroom.academicYear;
                                classroomDropdown.appendChild(option);
                            }
                        });
                    }
                });
        }

        function addAssignedClassToUI(className, subject, academicYear) {
            noClassesMessage.style.display = 'none';
            const badge = document.createElement('div');
            badge.className = 'flex items-center bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full';
            badge.dataset.className = className;
            badge.innerHTML = `
                <span>${className} - ${subject || 'N/A'}</span>
                <button type="button" class="ml-2 text-purple-600 hover:text-purple-800 remove-class-btn">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            badge.querySelector('.remove-class-btn').addEventListener('click', () => removeClassFromTeacherAPI(className));
            assignedClassesList.appendChild(badge);
        }
        
        assignClassBtn.addEventListener('click', function() {
            const classroomId = classroomDropdown.value;
            if (!classroomId) { alert('Please select a classroom.'); return; }

            const currentTeacherId = document.getElementById('teacherId').value;
            if (!currentTeacherId) { alert('Please save the teacher before assigning classes.'); return; }

            this.disabled = true;
            const selectedOption = classroomDropdown.options[classroomDropdown.selectedIndex];
            const className = selectedOption.dataset.name;
            const subject = selectedOption.dataset.subject;
            const academicYear = selectedOption.dataset.academicYear;
            
            const formData = new FormData();
            formData.append('teacherId', currentTeacherId);
            formData.append('classroomId', classroomId);

            fetch('<?php echo Yii::app()->createUrl('admin/assignClassToTeacher'); ?>', { 
                method: 'POST', 
                body: formData 
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    teacherAssignedClasses[className] = {
                        id: classroomId,
                        subject: subject,
                        academic_year: academicYear
                    };
                    
                    addAssignedClassToUI(className, subject, academicYear);
                    
                    classroomDropdown.value = '';
                    loadClassroomsForDropdown();
                    
                    displayFormMessage(true, `Class "${className}" successfully assigned to teacher.`);
                } else {
                    displayFormMessage(false, data.message || 'Failed to assign class.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayFormMessage(false, 'Error assigning class. Please try again.');
            })
            .finally(() => {
                this.disabled = false;
            });
        });

        function removeClassFromTeacherAPI(className) {
            const currentTeacherId = document.getElementById('teacherId').value;
            if (!currentTeacherId) {
                const badgeToRemove = assignedClassesList.querySelector(`div[data-class-name="${className}"]`);
                if (badgeToRemove) badgeToRemove.remove();
                delete teacherAssignedClasses[className];
                if (Object.keys(teacherAssignedClasses).length === 0) noClassesMessage.style.display = 'block';
                loadClassroomsForDropdown();
                return;
            }

            const formData = new FormData();
            formData.append('teacherId', currentTeacherId);
            formData.append('className', className);

            const badgeToRemove = assignedClassesList.querySelector(`div[data-class-name="${className}"]`);
            if (badgeToRemove) {
                const btn = badgeToRemove.querySelector('.remove-class-btn');
                if (btn) btn.disabled = true;
            }

            fetch('<?php echo Yii::app()->createUrl('admin/removeClassFromTeacher'); ?>', { 
                method: 'POST', 
                body: formData 
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (badgeToRemove) badgeToRemove.remove();
                    delete teacherAssignedClasses[className];
                    
                    if (Object.keys(teacherAssignedClasses).length === 0) {
                        noClassesMessage.style.display = 'block';
                    }
                    
                    loadClassroomsForDropdown();
                    displayFormMessage(true, `Class "${className}" successfully removed.`);
                } else {
                    if (badgeToRemove) {
                        const btn = badgeToRemove.querySelector('.remove-class-btn');
                        if (btn) btn.disabled = false;
                    }
                    displayFormMessage(false, data.message || 'Failed to remove class.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayFormMessage(false, 'Error removing class. Please try again.');
                if (badgeToRemove) {
                    const btn = badgeToRemove.querySelector('.remove-class-btn');
                    if (btn) btn.disabled = false;
                }
            });
        }

        document.getElementById('closeModal').addEventListener('click', () => teacherModal.classList.add('hidden'));
        document.getElementById('cancelForm').addEventListener('click', () => teacherModal.classList.add('hidden'));
        document.getElementById('closeViewModal').addEventListener('click', () => viewTeacherModal.classList.add('hidden'));
        document.getElementById('closeViewBtn').addEventListener('click', () => viewTeacherModal.classList.add('hidden'));
        document.getElementById('cancelDelete').addEventListener('click', () => deleteModal.classList.add('hidden'));

        document.getElementById('editFromView').addEventListener('click', function() {
            const id = document.getElementById('teacherInitials').getAttribute('data-id');
            viewTeacherModal.classList.add('hidden');
            const editButton = document.querySelector(`.edit-teacher[data-id="${id}"]`);
            if (editButton) editButton.click();
        });

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                if (row.cells.length === 1 && row.cells[0].colSpan === 5) return;
                row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
            });
        });
        
        document.getElementById('exportBtn').addEventListener('click', () => alert('Export functionality for teachers to be implemented.'));
    });
    </script>
</body>
</html>
