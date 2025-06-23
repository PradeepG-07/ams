<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">User Registration Form</h2>
    </div>
    
    <form id="user-form" class="p-6 space-y-6">
        <!-- Basic User Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                <input type="text" id="name" name="name" required maxlength="100"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div id="name-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div id="email-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                <input type="password" id="password" name="password" required minlength="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div id="password-error" class="text-red-500 text-sm mt-1 hidden"></div>
                <p class="text-gray-500 text-xs mt-1">Minimum 6 characters</p>
            </div>
            
            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                <select id="role" name="role" required onchange="toggleRoleFields()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>
                <div id="role-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>
        </div>
        
        <!-- Address Section -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                    <input type="text" id="address_line1" name="address[address_line1]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                    <input type="text" id="address_line2" name="address[address_line2]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" id="city" name="address[city]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" id="state" name="address[state]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="zip" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                    <input type="text" id="zip" name="address[zip]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" id="country" name="address[country]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Student Fields -->
        <div id="student-fields" class="border-t pt-6 hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="roll_no" class="block text-sm font-medium text-gray-700 mb-2">Roll Number</label>
                    <input type="text" id="roll_no" name="student[roll_no]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="cgpa" class="block text-sm font-medium text-gray-700 mb-2">CGPA</label>
                    <input type="number" id="cgpa" name="student[cgpa]" step="0.1" min="0" max="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <input type="text" id="class" name="student[class]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                    <input type="file" id="profile_picture" name="student[profile_picture]" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <!-- Hobbies Section -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hobbies</label>
                <div id="hobbies-container" class="space-y-3">
                    <div class="hobby-item flex gap-3">
                        <input type="text" name="student[hobbies][0][name]" placeholder="Hobby name"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <input type="text" name="student[hobbies][0][description]" placeholder="Description"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" onclick="removeHobby(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addHobby()" class="mt-3 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Add Hobby</button>
            </div>
        </div>
        
        <!-- Teacher Fields -->
        <div id="teacher-fields" class="border-t pt-6 hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Teacher Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="emp_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                    <input type="text" id="emp_id" name="teacher[emp_id]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">Salary</label>
                    <input type="number" id="salary" name="teacher[salary]" step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="md:col-span-2">
                    <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                    <input type="text" id="designation" name="teacher[designation]" maxlength="255"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <!-- Classes Section -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Classes</label>
                <div id="classes-container" class="space-y-3">
                    <div class="class-item flex gap-3">
                        <input type="text" name="teacher[classes][0]" placeholder="Class name"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" onclick="removeClass(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addClass()" class="mt-3 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Add Class</button>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="border-t pt-6">
            <button type="submit" class="w-full md:w-auto px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Register User
            </button>
        </div>
    </form>
</div>

<script>
let hobbyCount = 1;
let classCount = 1;

function toggleRoleFields() {
    const role = document.getElementById('role').value;
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

// Form validation and submission
document.getElementById('user-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    // Validate form
    let isValid = true;
    
    // Required fields validation
    const requiredFields = ['name', 'email', 'password', 'role'];
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            showError(field, 'This field is required');
            isValid = false;
        }
    });
    
    // Email validation
    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Password length validation
    const password = document.getElementById('password').value;
    if (password && password.length < 6) {
        showError('password', 'Password must be at least 6 characters long');
        isValid = false;
    }
    
    if (isValid) {
        // Submit form via AJAX or regular form submission
        const formData = new FormData(this);
        
        // Here you would typically send the data to your controller
        console.log('Form data ready for submission:', Object.fromEntries(formData));
        alert('Form validation passed! Ready for submission.');
    }
});

function showError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    const input = document.getElementById(fieldId);
    if (input) {
        input.classList.add('border-red-500');
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('[id$="-error"]');
    errorElements.forEach(element => {
        element.classList.add('hidden');
        element.textContent = '';
    });
    
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.classList.remove('border-red-500');
    });
}
</script>
