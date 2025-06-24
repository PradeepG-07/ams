<?php
/* @var $this StudentController */
/* @var $student array */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?php echo CHtml::encode($student['user']['name']); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="mr-4">
                            <?php if (!empty($student['profile_picture_url'])): ?>
                                <img src="<?php echo CHtml::encode($student['profile_picture_url']); ?>" 
                                     alt="Profile Picture" 
                                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-white text-blue-600 border-2 border-white shadow-md flex items-center justify-center">
                                    <span class="text-lg font-bold">
                                        <?php echo strtoupper(substr($student['user']['name'], 0, 1)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Welcome back, <?php echo CHtml::encode($student['user']['name']); ?>!</h1>
                            <p class="text-blue-100">Student Dashboard</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- CGPA Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold">📊</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Current CGPA</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo number_format($student['cgpa'], 2); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Class Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 font-bold">🎓</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Current Class</p>
                            <p class="text-lg font-bold text-gray-900">
                                <?php echo CHtml::encode($student['class_info']['class_name']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Roll Number Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-bold">🆔</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Roll Number</p>
                            <p class="text-lg font-bold text-gray-900">
                                <?php echo CHtml::encode($student['roll_no']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Account Status Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-600 font-bold">✅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Account Status</p>
                            <p class="text-lg font-bold text-green-600">Active</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Profile Summary -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Summary</h3>
                        
                        <div class="text-center mb-4">
                            <?php if (!empty($student['profile_picture_url'])): ?>
                                <img src="<?php echo CHtml::encode($student['profile_picture_url']); ?>" 
                                     alt="Profile Picture" 
                                     class="w-20 h-20 rounded-full object-cover border-4 border-blue-100 shadow-md mx-auto">
                            <?php else: ?>
                                <div class="w-20 h-20 rounded-full bg-blue-500 border-4 border-blue-100 shadow-md flex items-center justify-center mx-auto">
                                    <span class="text-white text-2xl font-bold">
                                        <?php echo strtoupper(substr($student['user']['name'], 0, 1)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Name:</span>
                                <span class="font-medium"><?php echo CHtml::encode($student['user']['name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium text-sm"><?php echo CHtml::encode($student['user']['email']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Role:</span>
                                <span class="font-medium capitalize"><?php echo CHtml::encode($student['user']['role']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Member Since:</span>
                                <span class="font-medium">
                                    <?php 
                                    $date = new DateTime();
                                    $date->setTimestamp($student['user']['created_at']->sec);
                                    echo $date->format('M Y');
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>

                   
                </div>

                <!-- Right Column - Details and Charts -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Academic Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">🎓</span>
                            Academic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Current CGPA</label>
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo ($student['cgpa'] / 4) * 100; ?>%"></div>
                                        </div>
                                        <span class="text-lg font-bold text-blue-600"><?php echo number_format($student['cgpa'], 2); ?>/4.0</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Class</label>
                                    <p class="text-gray-800 font-medium"><?php echo CHtml::encode($student['class_info']['class_name']); ?></p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Roll Number</label>
                                    <p class="text-gray-800 font-medium font-mono"><?php echo CHtml::encode($student['roll_no']); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Student ID</label>
                                    <p class="text-gray-800 font-medium font-mono text-sm"><?php echo CHtml::encode($student['_id']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="mr-2">📍</span>
                            Contact Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                                    <p class="text-gray-800"><?php echo CHtml::encode($student['user']['email']); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Address Line 1</label>
                                    <p class="text-gray-800"><?php echo CHtml::encode($student['user']['address']['address_line1'] ?? 'N/A'); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Address Line 2</label>
                                    <p class="text-gray-800"><?php echo CHtml::encode($student['user']['address']['address_line2'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">City</label>
                                    <p class="text-gray-800"><?php echo CHtml::encode($student['user']['address']['city'] ?? 'N/A'); ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">State</label>
                                    <p class="text-gray-800"><?php echo CHtml::encode($student['user']['address']['state'] ?? 'N/A'); ?></p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">ZIP Code</label>
                                        <p class="text-gray-800"><?php echo CHtml::encode($student['user']['address']['zip'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Country</label>
                                        <p class="text-gray-800"><?php echo CHtml::encode($student['user']['address']['country'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                </div>
            </div>
        </main>
    </div>

    <!-- Hidden file input for profile picture upload -->
    <input type="file" id="profilePictureInput" accept="image/*" style="display: none;" onchange="handleProfilePictureUpload(this)">

</body>
</html>