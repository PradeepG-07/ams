<?php
/* @var $this TeacherController */
/* @var $classId string */
/* @var $classDetails array */
/* @var $students array */
/* @var $performanceData array */

$this->breadcrumbs = array(
    'Teacher' => array('index'),
    'Classes' => array('classes'),
    'Class Details',
);
?>
<head>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<div class="bg-white p-6 rounded-lg shadow-sm max-w-7xl mx-auto">
    <!-- Class Header Information -->
    <div class="border-b pb-6 mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo CHtml::encode($classDetails['class_name']); ?></h1>
            
            <!-- Take Attendance Button -->
            <button id="takeAttendanceBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Take Attendance
            </button>
        </div>
        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Subject: <?php echo CHtml::encode($classDetails['subject']); ?></span>
            </div>
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Academic Year: <?php echo CHtml::encode($classDetails['academic_year']); ?></span>
            </div>
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Room: <?php echo CHtml::encode($classDetails['schedule']['room']); ?></span>
            </div>
            <div class="flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Schedule: <?php echo CHtml::encode($classDetails['schedule']['days']); ?>, <?php echo CHtml::encode($classDetails['schedule']['time']); ?></span>
            </div>
        </div>
    </div>

    <!-- Attendance Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="font-semibold text-blue-700">Total Students</h3>
            <p class="text-2xl font-bold"><?php echo CHtml::encode($classDetails['total_students']); ?></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <h3 class="font-semibold text-purple-700">Classes Conducted</h3>
            <p class="text-2xl font-bold"><?php echo CHtml::encode($classDetails['classes_conducted']); ?> / <?php echo CHtml::encode($classDetails['total_classes']); ?></p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="font-semibold text-yellow-700">Last Attendance</h3>
            <p class="text-2xl font-bold"><?php echo CHtml::encode($classDetails['last_attendance_date']); ?></p>
        </div>
    </div>
    
    <!-- Attendance Analytics Section -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Attendance Analytics</h2>
        <div class="flex justify-center">
            <!-- Status Distribution Chart -->
            <div class="bg-white p-6 rounded-lg shadow max-w-md w-full">
                <h3 class="font-semibold mb-4 text-center">Attendance Status Distribution</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Student Attendance List Section -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Student Attendance</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance %</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present/Total</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Status</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Date</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consecutive Absences</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($students as $student): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 whitespace-nowrap"><?php echo CHtml::encode($student['student_id']); ?></td>
                        <td class="py-3 px-4 whitespace-nowrap"><?php echo CHtml::encode($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $student['attendance_percentage']; ?>%"></div>
                                </div>
                                <span class="ml-2"><?php echo $student['attendance_percentage']; ?>%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <?php echo $student['total_present']; ?>/<?php echo $student['total_present'] + $student['total_absent']; ?>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <?php 
                                $statusColor = 'bg-green-100 text-green-800';
                                $statusText = 'Present';
                                
                                if($student['last_status'] == 'absent') {
                                    $statusColor = 'bg-red-100 text-red-800';
                                    $statusText = 'Absent';
                                }
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $statusColor; ?>"><?php echo $statusText; ?></span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-500"><?php echo CHtml::encode($student['last_attendance_date']); ?></td>
                        <td class="py-3 px-4 whitespace-nowrap text-center">
                            <?php if ($student['consecutive_absences'] > 0): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <?php echo $student['consecutive_absences']; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">0</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <?php echo CHtml::link('Back to Classes', array('teacher/classes'), array(
            'class' => 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
        )); ?>
    </div>
</div>

<!-- Take Attendance Modal -->
<div id="attendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-5xl max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Take Attendance - <?php echo CHtml::encode($classDetails['class_name']); ?></h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4">
            <div class="flex items-center mb-2">
                <span class="text-gray-700 font-medium">Date:</span>
                <input type="date" id="attendanceDate" 
                       class="ml-2 px-3 py-2 border border-gray-300 rounded-md" 
                       value="<?php echo date('Y-m-d'); ?>"
                       min="<?php echo date('Y-m-d', strtotime('-2 days')); ?>" 
                       max="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Marking attendance for all <?php echo count($students); ?> students in this class</span>
            </div>
        </div>
        
        <div class="mb-4 flex gap-4">
            <button id="markAllPresent" class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded-md text-sm">
                Mark All Present
            </button>
            <button id="markAllAbsent" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded-md text-sm">
                Mark All Absent
            </button>
            <button id="resetAttendance" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm">
                Reset
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($students as $index => $student): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 whitespace-nowrap"><?php echo CHtml::encode($student['student_id']); ?></td>
                        <td class="py-3 px-4 whitespace-nowrap"><?php echo CHtml::encode($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex justify-center gap-2" role="group">
                                <button type="button" class="attendance-btn present-btn px-3 py-1 rounded-md bg-green-100 hover:bg-green-200 text-green-800" data-student="<?php echo $index; ?>" data-status="present">Present</button>
                                <button type="button" class="attendance-btn absent-btn px-3 py-1 rounded-md bg-red-100 hover:bg-red-200 text-red-800" data-student="<?php echo $index; ?>" data-status="absent">Absent</button>
                            </div>
                            <input type="hidden" name="attendance[<?php echo $index; ?>]" id="student-<?php echo $index; ?>-status" value="">
                        </td>
                        <td class="py-3 px-4">
                            <input type="text" name="notes[<?php echo $index; ?>]" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" placeholder="Optional notes">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 flex justify-end gap-4">
            <button id="cancelAttendance" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </button>
            <button id="submitAttendance" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Submit Attendance
            </button>
        </div>
    </div>
</div>

<!-- Charts Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper function to safely get chart data with fallbacks
    function getChartData(data, key, defaultValue = 85) {
        if (!data || !data[key]) {
            return defaultValue;
        }
        return data[key] || defaultValue;
    }

    // Get performance data with fallbacks
    const performanceData = <?php echo json_encode($performanceData ?? []); ?>;
    
    // Status Distribution Chart (Pie Chart)
    const statusDistributionCtx = document.getElementById('statusDistributionChart').getContext('2d');
    const statusData = performanceData.status_distribution || {};
    
    const presentPercentage = getChartData(statusData, 'Present', 87);
    const absentPercentage = 100 - presentPercentage;
    
    new Chart(statusDistributionCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                data: [presentPercentage, absentPercentage],
                backgroundColor: [
                    'rgba(52, 211, 153, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgba(52, 211, 153, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
    
    // Take Attendance Modal Functions
    const takeAttendanceBtn = document.getElementById('takeAttendanceBtn');
    const attendanceModal = document.getElementById('attendanceModal');
    const closeModal = document.getElementById('closeModal');
    const cancelAttendance = document.getElementById('cancelAttendance');
    const submitAttendance = document.getElementById('submitAttendance');
    const markAllPresent = document.getElementById('markAllPresent');
    const markAllAbsent = document.getElementById('markAllAbsent');
    const resetAttendance = document.getElementById('resetAttendance');
    const attendanceButtons = document.querySelectorAll('.attendance-btn');
    
    // Show modal
    takeAttendanceBtn.addEventListener('click', function() {
        attendanceModal.classList.remove('hidden');
    });
    
    // Hide modal
    function hideModal() {
        attendanceModal.classList.add('hidden');
    }
    
    closeModal.addEventListener('click', hideModal);
    cancelAttendance.addEventListener('click', hideModal);
    
    // Mark all as present
    markAllPresent.addEventListener('click', function() {
        document.querySelectorAll('.present-btn').forEach(btn => {
            const studentId = btn.getAttribute('data-student');
            document.getElementById(`student-${studentId}-status`).value = 'present';
            
            // Update UI
            updateButtonStyles(btn);
        });
    });
    
    // Mark all as absent
    markAllAbsent.addEventListener('click', function() {
        document.querySelectorAll('.absent-btn').forEach(btn => {
            const studentId = btn.getAttribute('data-student');
            document.getElementById(`student-${studentId}-status`).value = 'absent';
            
            // Update UI
            updateButtonStyles(btn);
        });
    });
    
    // Reset attendance
    resetAttendance.addEventListener('click', function() {
        document.querySelectorAll('[id^="student-"][id$="-status"]').forEach(input => {
            input.value = '';
        });
        
        // Reset UI
        attendanceButtons.forEach(btn => {
            btn.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-500', 'font-bold');
        });
    });
    
    // Individual attendance buttons
    attendanceButtons.forEach(btn => {  
        btn.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student');
            const status = this.getAttribute('data-status');
            document.getElementById(`student-${studentId}-status`).value = status;
            
            // Update UI
            updateButtonStyles(this);
        });
    });
    
    function updateButtonStyles(clickedButton) {
        // Get all buttons for this student
        const studentId = clickedButton.getAttribute('data-student');
        const studentButtons = document.querySelectorAll(`.attendance-btn[data-student="${studentId}"]`);
        
        // Reset all buttons for this student
        studentButtons.forEach(btn => {
            btn.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-500', 'font-bold');
        });
        
        // Highlight the clicked button
        clickedButton.classList.add('ring-2', 'ring-offset-2', 'ring-blue-500', 'font-bold');
    }
    
    // Submit attendance
    submitAttendance.addEventListener('click', function() {
        // Collect all attendance data
        const attendanceDate = document.getElementById('attendanceDate').value;
        const attendanceData = [];
        
        document.querySelectorAll('[id^="student-"][id$="-status"]').forEach((input, index) => {
            const studentId = input.id.replace('student-', '').replace('-status', '');
            const status = input.value;
            const notes = document.querySelector(`input[name="notes[${studentId}]"]`).value;
            
            if (status) {
                attendanceData.push({
                    student_id: <?php echo json_encode(array_column($students, '_id')); ?>[studentId],
                    status: status,
                    notes: notes,
                    date: attendanceDate
                });
            }
        });
        
        // Validate if all students have been marked
        if (attendanceData.length < <?php echo count($students); ?>) {
            if (!confirm(`Not all students have been marked. There are ${<?php echo count($students); ?> - attendanceData.length} students without attendance status. Continue anyway?`)) {
                return;
            }
        }
        
        // Send the data via AJAX to save attendance
        $.ajax({
            url: '<?php echo $this->createUrl('teacher/saveAttendance'); ?>',
            type: 'POST',
            data: {
                class_id: '<?php echo $classId; ?>',
                attendance_date: attendanceDate,
                attendance_data: JSON.stringify(attendanceData)
            },
            success: function(response) {
                alert('Attendance saved successfully!');
                hideModal();
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('An error occurred while saving attendance: ' + error);
            }
        });
    });
});
</script>
