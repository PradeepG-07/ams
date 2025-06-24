<?php
/* @var $this ClassesController */
/* @var $classes array */

$this->pageTitle = Yii::app()->name . ' - Classes List';
$this->breadcrumbs = array(
    'Classes',
);
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Classes</h2>
            <a href="<?php echo Yii::app()->createUrl('classes/create'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Add New Class
            </a>
        </div>
        
        <!-- Loading Spinner -->
        <div id="loading" class="hidden flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        
        <!-- Classes Table -->
        <div id="classes-table" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="classes-tbody" class="bg-white divide-y divide-gray-200">
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
    loadClasses(1);
});

function loadClasses(page) {
    showLoading(true);
    
    fetch(`<?= Yii::app()->createUrl('classes/index') ?>?page=${page}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTable(data.classes);
            updatePagination(page, data.total);
            currentPage = page;
        } else {
            console.error('Error loading classes:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        showLoading(false);
    });
}

function renderTable(classes) {
    const tbody = document.getElementById('classes-tbody');
    tbody.innerHTML = '';
    
    if (classes.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                No classes found. Add a class using the button above.
            </td>
        `;
        tbody.appendChild(row);
        return;
    }
    
    classes.forEach(classItem => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const teacherName = classItem.teacher_name || 'Not Assigned';
        const studentCount = classItem.students ? Object.keys(classItem.students).length : 0;
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-800 font-semibold">
                                ${classItem.class_name.substring(0, 2).toUpperCase()}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            ${classItem.class_name}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <a href="<?= Yii::app()->createUrl('classes/update') ?>?id=${classItem._id.$oid || classItem._id}" 
                       class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 py-1 px-3 rounded-md transition duration-200">
                        Edit
                    </a>
                    <button onclick="confirmDelete('${classItem._id.$oid || classItem._id}', '${classItem.class_name}')"
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
    if (confirm(`Are you sure you want to delete class "${name}"?`)) {
        deleteClass(id);
    }
}

function deleteClass(id) {
    showLoading(true);
    
    fetch(`<?= Yii::app()->createUrl('classes/delete') ?>?id=${id}`, {
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
            loadClasses(currentPage);
            
            // Show success message
            showNotification('Class deleted successfully', 'success');
        } else {
            console.error('Error deleting class:', data.message);
            showNotification('Error deleting class: ' + data.message, 'error');
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
    
    prevMobile.onclick = () => page > 1 && loadClasses(page - 1);
    nextMobile.onclick = () => page < totalPages && loadClasses(page + 1);
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
        button.onclick = () => loadClasses(pageNum);
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
    const table = document.getElementById('classes-table');
    
    if (show) {
        loading.classList.remove('hidden');
        table.classList.add('opacity-50');
    } else {
        loading.classList.add('hidden');
        table.classList.remove('opacity-50');
    }
}
</script>