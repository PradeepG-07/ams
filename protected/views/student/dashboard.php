<?php
/* @var $this StudentController */
/* @var $student Student */
/* @var $attendanceData array */
/* @var $recentNotifications array */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <!-- Profile Picture -->
                        <div class="mr-4">
                            <?php if (!empty($student->profile_picture)): ?>
                                <img src="<?php echo CHtml::encode(S3Helper::generateGETObjectUrl($student->profile_picture)); ?>" 
                                     alt="Profile Picture" 
                                     class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-full bg-blue-500 border-2 border-white shadow-md flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Student Dashboard</h1>
                            <p class="text-blue-100">Welcome back, <?php echo CHtml::encode($student->first_name . ' ' . $student->last_name); ?>!</p>
                        </div>
                    </div>
                    <div>
                        <div class="text-right">
                            <p class="font-semibold"><?php echo CHtml::encode($student->roll_no); ?></p>
                            <p class="text-sm text-blue-100"><?php echo CHtml::encode($student->email); ?></p>
                            <a href="<?php echo $this->createUrl('profile'); ?>" class="text-sm text-blue-200 hover:text-white underline mt-1 inline-block">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Statistics Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Overall Attendance Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Attendance</h2>
                        <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded">Overall</span>
                    </div>
                    <div class="flex items-center">
                        <div class="relative w-20 h-20 mr-4">
                            <svg viewBox="0 0 36 36" class="w-full h-full">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e6e6e6" stroke-width="3" />
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#4ade80" stroke-width="3" stroke-dasharray="<?php echo $attendanceData['overall']; ?>, 100" />
                                <text x="18" y="20.5" text-anchor="middle" fill="#374151" font-size="10"><?php echo $attendanceData['overall']; ?>%</text>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Present</p>
                            <p class="text-xl font-bold text-gray-800"><?php echo $attendanceData['overall']; ?>%</p>
                        </div>
                    </div>
                </div>

                <!-- Classes Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Classes</h2>
                        <span class="text-xs font-medium bg-amber-100 text-amber-800 px-2 py-1 rounded">Enrolled</span>
                    </div>
                    <div class="flex items-center">
                        <div class="bg-amber-100 rounded-full p-4 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Classes</p>
                            <p class="text-xl font-bold text-gray-800"><?php echo count($student->classes); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Detailed Data -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Attendance by Subject Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Attendance by Subject</h2>
                    <div class="h-64 mx-auto">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
                
                <!-- Attendance Status Distribution Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Attendance Status Distribution</h2>
                    <div class="h-64 mx-auto">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Additional Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Attendance History Trend Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Attendance History Trend</h2>
                    <div class="h-64 mx-auto">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Class Information</h2>
                    <div class="space-y-4">
                        <?php foreach ($student->classes as $className => $classInfo): ?>
                            <div class="border-b border-gray-100 pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-800"><?php echo CHtml::encode($className); ?></h3>
                                        <p class="text-sm text-gray-500">Code: <?php echo CHtml::encode($classInfo['id']); ?></p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span>Attendance</span>
                                        <span class="font-medium"><?php echo $attendanceData['bySubject'][$className]; ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1">
                                        <div class="bg-blue-500 rounded-full h-1" style="width: <?php echo $attendanceData['bySubject'][$className]; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
            </div>
        </main>
    </div>

    <script>
        // Initialize Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($attendanceData['bySubject'])); ?>,
                datasets: [{
                    label: 'Attendance (%)',
                    data: <?php echo json_encode(array_values($attendanceData['bySubject'])); ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1,
                    maxBarThickness: 60,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
        
        // Initialize Status Distribution Chart
        const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
        const statusDistributionChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    data: [
                        <?php 
                            $present = 0; $absent = 0; $late = 0;
                            foreach ($attendanceData['history'] as $record) {
                                if ($record['status'] === 'present') $present++;
                                elseif ($record['status'] === 'absent') $absent++;
                                elseif ($record['status'] === 'late') $late++;
                            }
                            echo "$present, $absent, $late";
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(245, 158, 11, 0.7)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 11, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Initialize Attendance Trend Chart
        const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        const attendanceTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php 
                    $dates = array_map(function($item) {
                        return date('M d', strtotime($item['date']));
                    }, $attendanceData['history']);
                    $dates = array_reverse($dates); // Reverse to show latest first
                    echo json_encode($dates); 
                ?>,
                datasets: [{
                    label: 'Attendance Status',
                    data: <?php 
                        $statusValues = array_map(function($item) {
                            switch($item['status']) {
                                case 'present': return 100;
                                case 'late': return 70;
                                case 'absent': return 0;
                                default: return 0;
                            }
                        }, $attendanceData['history']);
                        echo json_encode($statusValues); 
                    ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.2,
                    pointBackgroundColor: <?php 
                        $pointColors = array_map(function($item) {
                            switch($item['status']) {
                                case 'present': return '"rgba(16, 185, 129, 1)"';
                                case 'late': return '"rgba(245, 158, 11, 1)"';
                                case 'absent': return '"rgba(239, 68, 68, 1)"';
                                default: return '"rgba(107, 114, 128, 1)"';
                            }
                        }, $attendanceData['history']);
                        echo '[' . implode(',', $pointColors) . ']'; 
                    ?>
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return 'Absent';
                                if (value === 70) return 'Late';
                                if (value === 100) return 'Present';
                                return '';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                if (value === 0) return 'Absent';
                                if (value === 70) return 'Late';
                                if (value === 100) return 'Present';
                                return '';
                            }
                        }
                    }
                }
            }
        });
        
       
    </script>
</body>
</html>
