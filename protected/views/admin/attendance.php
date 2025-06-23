<?php
/* @var $this TeacherController */
/* @var $classes array */
/* @var $students array */
/* @var $selectedClass string */

$this->pageTitle = Yii::app()->name . ' - Take Attendance';
$this->breadcrumbs = array(
    'Teacher' => array('index'),
    'Classes' => array('classes'),
    'Take Attendance'
);
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Take Attendance</h1>

    <!-- Class Selection -->
    <div class="mb-6">
        <label for="class-select" class="block text-sm font-medium text-gray-700 mb-2">Select Class:</label>
        <select id="class-select" onchange="location.href='?class_id=' + this.value" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="">Select a class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo CHtml::encode($class['id']); ?>" <?php echo $class['id'] === $selectedClass ? 'selected' : ''; ?>>
                    <?php echo CHtml::encode($class['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($selectedClass && !empty($students)): ?>
        <div class="mb-6">
            <!-- Date Selection and Bulk Actions -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <label for="attendance-date" class="block text-sm font-medium text-gray-700">Date:</label>
                    <input type="date" id="attendance-date" value="<?php echo date('Y-m-d'); ?>" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="flex space-x-2">
                    <button onclick="markAllAs('present')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Mark All Present
                    </button>
                    <button onclick="markAllAs('absent')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Mark All Absent
                    </button>
                </div>
            </div>

            <!-- Students Table -->
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Student Name</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($students as $student): ?>
                            <tr id="student-row-<?php echo $student['id']; ?>" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                    <?php echo CHtml::encode($student['name']); ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <button onclick="markAttendance('<?php echo $student['id']; ?>', 'present')" 
                                                class="attendance-btn present-btn px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 hover:bg-green-200">
                                            Present
                                        </button>
                                        <button onclick="markAttendance('<?php echo $student['id']; ?>', 'absent')" 
                                                class="attendance-btn absent-btn px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 hover:bg-red-200">
                                            Absent
                                        </button>
                                    </div>
                                    <input type="hidden" id="status-<?php echo $student['id']; ?>" value="">
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <input type="text" id="notes-<?php echo $student['id']; ?>" 
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                           placeholder="Optional notes">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <button onclick="submitAttendance()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Attendance
                </button>
            </div>
        </div>

        <script>
            function markAttendance(studentId, status) {
                // Update hidden input
                document.getElementById(`status-${studentId}`).value = status;
                
                // Update UI
                const row = document.getElementById(`student-row-${studentId}`);
                const presentBtn = row.querySelector('.present-btn');
                const absentBtn = row.querySelector('.absent-btn');
                
                presentBtn.classList.remove('ring-2', 'ring-green-500');
                absentBtn.classList.remove('ring-2', 'ring-red-500');
                
                if (status === 'present') {
                    presentBtn.classList.add('ring-2', 'ring-green-500');
                } else {
                    absentBtn.classList.add('ring-2', 'ring-red-500');
                }
            }

            function markAllAs(status) {
                document.querySelectorAll('tr[id^="student-row-"]').forEach(row => {
                    const studentId = row.id.replace('student-row-', '');
                    markAttendance(studentId, status);
                });
            }

            function submitAttendance() {
                const attendanceDate = document.getElementById('attendance-date').value;
                const attendanceData = [];

                document.querySelectorAll('tr[id^="student-row-"]').forEach(row => {
                    const studentId = row.id.replace('student-row-', '');
                    const status = document.getElementById(`status-${studentId}`).value;
                    const notes = document.getElementById(`notes-${studentId}`).value;

                    if (status) {
                        attendanceData.push({
                            student_id: studentId,
                            status: status,
                            notes: notes
                        });
                    }
                });

                if (attendanceData.length === 0) {
                    alert('Please mark attendance for at least one student.');
                    return;
                }

                // Send data to server
                fetch('<?php echo $this->createUrl("teacher/saveAttendance"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        class_id: '<?php echo $selectedClass; ?>',
                        attendance_date: attendanceDate,
                        attendance_data: JSON.stringify(attendanceData)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Attendance saved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving attendance.');
                });
            }
        </script>
    <?php else: ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Please select a class to view students and mark attendance.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>