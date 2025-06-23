<?php
/* @var $this AdminController */

$this->breadcrumbs = array(
    'Admin' => array('index'),
    'Manage Attendance',
);

$this->menu = array(
    array('label' => 'Manage Students', 'url' => array('managestudents')),
    array('label' => 'Manage Teachers', 'url' => array('manageteachers')),
    array('label' => 'Manage Classes', 'url' => array('manageclasses')),
);
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manage Attendance</h1>

    <!-- Class Selection -->
    <div class="mb-6">
        <label for="class-select" class="block text-sm font-medium text-gray-700 mb-2">Select Class:</label>
        <select id="class-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="">Select a class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo CHtml::encode($class->_id); ?>">
                    <?php echo CHtml::encode($class->class_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Date Selection -->
    <div class="mb-6">
        <label for="attendance-date" class="block text-sm font-medium text-gray-700 mb-2">Select Date:</label>
        <input type="date" id="attendance-date" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
    </div>

    <!-- Attendance Table -->
    <div id="attendance-table" class="hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="student-list">
                    <!-- Students will be populated here -->
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button id="save-attendance" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Save Attendance
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('class-select').addEventListener('change', function() {
    const classId = this.value;
    if (classId) {
        // Load students for selected class
        fetch(`/admin/getStudents?classId=${classId}`)
            .then(response => response.json())
            .then(data => {
                const studentList = document.getElementById('student-list');
                studentList.innerHTML = '';
                
                data.students.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${student.name}</div>
                            <div class="text-sm text-gray-500">${student.roll_no}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select class="attendance-status mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" class="attendance-notes mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        </td>
                    `;
                    studentList.appendChild(row);
                });
                
                document.getElementById('attendance-table').classList.remove('hidden');
            });
    } else {
        document.getElementById('attendance-table').classList.add('hidden');
    }
});

document.getElementById('save-attendance').addEventListener('click', function() {
    const classId = document.getElementById('class-select').value;
    const date = document.getElementById('attendance-date').value;
    const attendanceData = [];
    
    document.querySelectorAll('#student-list tr').forEach(row => {
        attendanceData.push({
            student_id: row.dataset.studentId,
            status: row.querySelector('.attendance-status').value,
            notes: row.querySelector('.attendance-notes').value
        });
    });
    
    fetch('/admin/saveAttendance', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            class_id: classId,
            date: date,
            attendance: attendanceData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Attendance saved successfully!');
        } else {
            alert('Error saving attendance: ' + data.message);
        }
    });
});
</script>