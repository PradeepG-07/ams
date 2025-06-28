<?php
/* @var $this StudentController */
/* @var $student array */
/* @var $totalSessions int */
/* @var $sessionsAttended int */
/* @var $attendancePercentage float */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo CHtml::encode($student['user']['name']); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <?php if (!empty($student['profile_picture_url'])): ?>
                    <img src="<?php echo CHtml::encode($student['profile_picture_url']); ?>" 
                         alt="Profile Picture" 
                         class="w-16 h-16 rounded-full object-cover border-2 border-blue-200">
                <?php else: ?>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-xl font-semibold">
                            <?php echo strtoupper(substr($student['user']['name'], 0, 2)); ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo CHtml::encode($student['user']['name']); ?></h1>
                    <p class="text-gray-600"><?php echo CHtml::encode($student['class_info']['class_name']); ?> • Roll: <?php echo CHtml::encode($student['roll_no']); ?></p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- CGPA Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">CGPA</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo number_format($student['cgpa'], 2); ?></p>
                        <p class="text-sm text-gray-500">out of 4.0</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Attendance Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Attendance</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo number_format($attendancePercentage, 1); ?>%</p>
                        <p class="text-sm text-gray-500"><?php echo $sessionsAttended; ?>/<?php echo $totalSessions; ?> sessions</p>
                    </div>
                    <div class="w-12 h-12 <?php echo $attendancePercentage >= 75 ? 'bg-green-100' : ($attendancePercentage >= 50 ? 'bg-yellow-100' : 'bg-red-100'); ?> rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 <?php echo $attendancePercentage >= 75 ? 'text-green-600' : ($attendancePercentage >= 50 ? 'text-yellow-600' : 'text-red-600'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sessions Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Sessions</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $totalSessions; ?></p>
                        <p class="text-sm text-gray-500">this semester</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Profile Information -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Name</span>
                        <span class="text-sm text-gray-900"><?php echo CHtml::encode($student['user']['name']); ?></span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Email</span>
                        <span class="text-sm text-gray-900"><?php echo CHtml::encode($student['user']['email']); ?></span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Class</span>
                        <span class="text-sm text-gray-900"><?php echo CHtml::encode($student['class_info']['class_name']); ?></span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-600">Roll Number</span>
                        <span class="text-sm text-gray-900 font-mono"><?php echo CHtml::encode($student['roll_no']); ?></span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-sm font-medium text-gray-600">CGPA</span>
                        <span class="text-sm text-gray-900 font-semibold"><?php echo number_format($student['cgpa'], 2); ?>/4.0</span>
                    </div>
                </div>
            </div>

            <!-- Attendance Details -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Attendance Overview</h2>
                    <?php if (!empty($student['class_info']['_id'])): ?>
                        <a href="<?php echo $this->createUrl('attendanceRange'); ?>" 
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View Details →
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Attendance Progress -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Overall Attendance</span>
                        <span class="text-sm font-bold text-gray-900"><?php echo number_format($attendancePercentage, 1); ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="<?php echo $attendancePercentage >= 75 ? 'bg-green-500' : ($attendancePercentage >= 50 ? 'bg-yellow-500' : 'bg-red-500'); ?> h-3 rounded-full transition-all duration-300" 
                             style="width: <?php echo min($attendancePercentage, 100); ?>%"></div>
                    </div>
                </div>

                <!-- Attendance Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600"><?php echo $sessionsAttended; ?></p>
                        <p class="text-xs text-green-700 font-medium">Attended</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <p class="text-2xl font-bold text-red-600"><?php echo $totalSessions - $sessionsAttended; ?></p>
                        <p class="text-xs text-red-700 font-medium">Missed</p>
                    </div>
                </div>

                <!-- Attendance Status -->
                <div class="mt-4 p-3 rounded-lg <?php echo $attendancePercentage >= 75 ? 'bg-green-50 border border-green-200' : ($attendancePercentage >= 50 ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200'); ?>">
                    <p class="text-sm font-medium <?php echo $attendancePercentage >= 75 ? 'text-green-800' : ($attendancePercentage >= 50 ? 'text-yellow-800' : 'text-red-800'); ?>">
                        <?php 
                        if ($attendancePercentage >= 75) {
                            echo "Great attendance! Keep it up.";
                        } elseif ($attendancePercentage >= 50) {
                            echo "Attendance needs improvement.";
                        } else {
                            echo "Critical: Low attendance rate.";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>