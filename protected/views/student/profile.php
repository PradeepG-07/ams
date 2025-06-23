<?php
/* @var $this StudentController */
/* @var $student Student */
// echo $_ENV['S3_BUCKET_NAME'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Student Profile</h1>
                        <p class="text-blue-100">Manage your personal information</p>
                    </div>
                    <div>
                        <a href="<?php echo Yii::app()->createUrl('student/dashboard'); ?>" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if(Yii::app()->user->hasFlash('success')): ?>
            <div class="container mx-auto px-4 pt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline"><?php echo Yii::app()->user->getFlash('success'); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if(Yii::app()->user->hasFlash('error')): ?>
            <div class="container mx-auto px-4 pt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline"><?php echo Yii::app()->user->getFlash('error'); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                
                <!-- Profile Picture Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-6 text-center">Profile Picture</h2>
                        
                        <div class="flex flex-col items-center">
                            <!-- Current Profile Picture -->
                            <div class="relative mb-6">
                                <?php if (!empty($student->profile_picture)): ?>
                                    <img src="<?php echo CHtml::encode(S3Helper::generateGETObjectUrl($student->profile_picture)); ?>" 
                                         alt="Profile Picture" 
                                         class="w-32 h-32 rounded-full border-4 border-blue-200 shadow-lg object-cover">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student->first_name . ' ' . $student->last_name); ?>&size=150&background=random" 
                                         alt="Profile Picture" 
                                         class="w-32 h-32 rounded-full border-4 border-blue-200 shadow-lg">
                                <?php endif; ?>
                                
                            </div>

                            <?php if (empty($student->profile_picture)): ?>
                            <!-- Upload Form - Only show if no picture exists -->
                            <div x-data="{ uploading: false }" class="w-full">
                                <form action="<?php echo Yii::app()->createUrl('student/uploadProfilePicture'); ?>" 
                                      method="post" 
                                      enctype="multipart/form-data"
                                      @submit="uploading = true">
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Choose Profile Picture
                                        </label>
                                        <input type="file" 
                                               name="profile_picture" 
                                               accept="image/*" 
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                               required>
                                    </div>
                                    
                                    <button type="submit" 
                                            :disabled="uploading"
                                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center justify-center">
                                        <span x-show="!uploading">Upload Picture</span>
                                        <span x-show="uploading" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Uploading...
                                        </span>
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <!-- Profile Picture Management Options -->
                            <div class="w-full space-y-4">
                                <!-- Change Picture Form -->
                                <div x-data="{ uploading: false, showChangeForm: false }" class="w-full">
                                    <button @click="showChangeForm = !showChangeForm" 
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200 mb-3">
                                        <span x-show="!showChangeForm">Change Picture</span>
                                        <span x-show="showChangeForm">Cancel</span>
                                    </button>
                                    
                                    <div x-show="showChangeForm" x-transition class="space-y-3">
                                        <form action="<?php echo Yii::app()->createUrl('student/uploadProfilePicture'); ?>" 
                                              method="post" 
                                              enctype="multipart/form-data"
                                              @submit="uploading = true">
                                            
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Choose New Picture
                                                </label>
                                                <input type="file" 
                                                       name="profile_picture" 
                                                       accept="image/*" 
                                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                       required>
                                            </div>
                                            
                                            <button type="submit" 
                                                    :disabled="uploading"
                                                    class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-bold py-2 px-4 rounded transition duration-200 flex items-center justify-center">
                                                <span x-show="!uploading">Update Picture</span>
                                                <span x-show="uploading" class="flex items-center">
                                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Updating...
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Remove Picture Button -->
                                <div x-data="{ showConfirm: false }">
                                    <button @click="showConfirm = true" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                        Remove Picture
                                    </button>
                                    
                                    <!-- Confirmation Modal -->
                                    <div x-show="showConfirm" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                         @click.away="showConfirm = false">
                                        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Removal</h3>
                                            <p class="text-gray-600 mb-6">Are you sure you want to remove your profile picture? This action cannot be undone.</p>
                                            <div class="flex space-x-3">
                                                <button @click="showConfirm = false" 
                                                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                                    Cancel
                                                </button>
                                                <a href="<?php echo Yii::app()->createUrl('student/removeProfilePicture'); ?>" 
                                                   class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-center">
                                                    Remove
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-700">Personal Information</h2>
                            <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded">Student Profile</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-gray-800 font-semibold">
                                            <?php echo CHtml::encode($student->first_name . ' ' . $student->last_name); ?>
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Roll Number</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-gray-800 font-semibold"><?php echo CHtml::encode($student->roll_no); ?></p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-gray-800"><?php echo CHtml::encode($student->email); ?></p>
                                    </div>
                                </div>

                                <?php if(isset($student->phone)): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-gray-800"><?php echo CHtml::encode($student->phone); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                                <?php if(isset($student->date_of_birth)): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Date of Birth</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-gray-800"><?php echo CHtml::encode(date('F d, Y', strtotime($student->date_of_birth))); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if(isset($student->address) && $student->address instanceof Address): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <?php 
                                        $addressParts = array();
                                        if (!empty($student->address->address_line1)) {
                                            $addressParts[] = CHtml::encode($student->address->address_line1);
                                        }
                                        if (!empty($student->address->address_line2)) {
                                            $addressParts[] = CHtml::encode($student->address->address_line2);
                                        }
                                        if (!empty($student->address->city)) {
                                            $addressParts[] = CHtml::encode($student->address->city);
                                        }
                                        if (!empty($student->address->state)) {
                                            $addressParts[] = CHtml::encode($student->address->state) . ' ' . CHtml::encode($student->address->zip);
                                        }
                                        if (!empty($student->address->country)) {
                                            $addressParts[] = CHtml::encode($student->address->country);
                                        }
                                        
                                        if (!empty($addressParts)) {
                                            echo '<p class="text-gray-800">' . implode('<br>', $addressParts) . '</p>';
                                        } else {
                                            echo '<p class="text-gray-500 italic">No address information</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Student ID</label>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-gray-800 font-mono text-sm"><?php echo CHtml::encode((string)$student->_id); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           

    
            
        </main>
    </div>
</body>
</html>