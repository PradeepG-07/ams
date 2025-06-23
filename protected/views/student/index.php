<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Students</h2>
        </div>
        
        <!-- Loading Spinner -->
        <div id="loading" class="hidden flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        
        <!-- Students Table -->
        <div id="students-table" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CGPA</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hobbies</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="students-tbody" class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated here -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="prev-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button id="next-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span id="showing-from" class="font-medium">1</span> to <span id="showing-to" class="font-medium">5</span> of <span id="total-records" class="font-medium">0</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" id="pagination-nav">
                        <!-- Pagination buttons will be generated here -->
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let totalRecords = 0;

document.addEventListener('DOMContentLoaded', function() {
    loadStudents(1);
});

function loadStudents(page) {
    showLoading(true);
    
    fetch(`<?= Yii::app()->createUrl('student/index') ?>?page=${page}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTable(data.students);
            updatePagination(page, data.total);
            currentPage = page;
        } else {
            console.error('Error loading students:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        showLoading(false);
    });
}


function renderTable(students) {
    const tbody = document.getElementById('students-tbody');
    tbody.innerHTML = '';
    
    students.forEach(student => {
        console.log(student);
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const hobbies = student.hobbies ? student.hobbies.map(h => h.name).join(', ') : 'N/A';
        const profilePicture = student.profile_picture ? 
            `<img src="/uploads/profiles/${student.profile_picture}" alt="Profile" class="w-10 h-10 rounded-full object-cover">` :
            `<div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm font-medium">${student.user.name.charAt(0)}</div>`;
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                ${profilePicture}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${student.roll_no}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${student.user.name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${student.user.email}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${student.class}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    ${student.cgpa}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${hobbies}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex space-x-2">
                    <a href="<?= Yii::app()->createUrl('student/update') ?>?id=${student._id.$oid}" 
                       class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 py-1 px-3 rounded-md transition duration-200">
                        Edit
                    </a>
                    <button 
                       onclick="confirmDelete('${student.user_id.$oid}', '${student.user.name}')"
                       class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 py-1 px-3 rounded-md transition duration-200">
                        Delete
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to delete student "${name}"?`)) {
        deleteStudent(id);
    }
}

function deleteStudent(id) {
    showLoading(true);
    
    fetch(`<?= Yii::app()->createUrl('user/delete') ?>?id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the current page to reflect the changes
            loadStudents(currentPage);
            
            // Show success message
            showNotification('Student deleted successfully', 'success');
        } else {
            console.error('Error deleting student:', data.message);
            showNotification('Error deleting student: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An unexpected error occurred', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

// Simple notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    } transition-opacity duration-500`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

function updatePagination(page, total) {
    totalRecords = total;
    totalPages = Math.ceil(total / 5);
    
    // Update showing text
    const showingFrom = (page - 1) * 5 + 1;
    const showingTo = Math.min(page * 5, total);
    
    document.getElementById('showing-from').textContent = showingFrom;
    document.getElementById('showing-to').textContent = showingTo;
    document.getElementById('total-records').textContent = total;
    
    // Generate pagination buttons
    const paginationNav = document.getElementById('pagination-nav');
    paginationNav.innerHTML = '';
    
    // Previous button
    const prevBtn = createPaginationButton('Previous', page - 1, page === 1);
    paginationNav.appendChild(prevBtn);
    
    // Page number buttons
    const startPage = Math.max(1, page - 2);
    const endPage = Math.min(totalPages, page + 2);
    
    if (startPage > 1) {
        paginationNav.appendChild(createPaginationButton('1', 1));
        if (startPage > 2) {
            paginationNav.appendChild(createEllipsis());
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        paginationNav.appendChild(createPaginationButton(i.toString(), i, false, i === page));
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationNav.appendChild(createEllipsis());
        }
        paginationNav.appendChild(createPaginationButton(totalPages.toString(), totalPages));
    }
    
    // Next button
    const nextBtn = createPaginationButton('Next', page + 1, page === totalPages);
    paginationNav.appendChild(nextBtn);
    
    // Update mobile buttons
    const prevMobile = document.getElementById('prev-mobile');
    const nextMobile = document.getElementById('next-mobile');
    
    prevMobile.disabled = page === 1;
    nextMobile.disabled = page === totalPages;
    
    prevMobile.onclick = () => page > 1 && loadStudents(page - 1);
    nextMobile.onclick = () => page < totalPages && loadStudents(page + 1);
}

function createPaginationButton(text, pageNum, disabled = false, active = false) {
    const button = document.createElement('button');
    button.textContent = text;
    button.className = `relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
        active 
            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
            : disabled
                ? 'bg-white border-gray-300 text-gray-300 cursor-not-allowed'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
    }`;
    
    if (!disabled && !active) {
        button.onclick = () => loadStudents(pageNum);
    }
    
    button.disabled = disabled;
    return button;
}

function createEllipsis() {
    const span = document.createElement('span');
    span.textContent = '...';
    span.className = 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700';
    return span;
}

function showLoading(show) {
    const loading = document.getElementById('loading');
    const table = document.getElementById('students-table');
    
    if (show) {
        loading.classList.remove('hidden');
        table.classList.add('opacity-50');
    } else {
        loading.classList.add('hidden');
        table.classList.remove('opacity-50');
    }
}
</script>
