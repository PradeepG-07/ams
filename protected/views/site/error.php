<?php
/* @var $this SiteController */
/* @var $error array */
 
$this->pageTitle = Yii::app()->name . ' - Error';
$this->breadcrumbs = array(
    'Error',
);
?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Error Icon -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-gray-900 mb-2"><?php echo CHtml::encode($code); ?></h1>
            
            <!-- Error Message -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">
                <?php 
                    // Custom error titles based on code
                    switch($code) {
                        case 404:
                            echo 'Page Not Found';
                            break;
                        case 403:
                            echo 'Access Forbidden';
                            break;
                        case 500:
                            echo 'Server Error';
                            break;
                        default:
                            echo 'An Error Occurred';
                    }
                ?>
            </h2>
            
            <!-- Error Description -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-700 text-sm"><?php echo CHtml::encode($message); ?></p>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-4">
                <?php if (!Yii::app()->user->isGuest): ?>
                    <!-- Logged in user buttons -->
                    <?php if (Yii::app()->user->isAdmin()): ?>
                        <a href="<?php echo Yii::app()->createUrl('admin/managestudents'); ?>" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Go to Admin Dashboard
                        </a>
                    <?php elseif (Yii::app()->user->isTeacher()): ?>
                        <a href="<?php echo Yii::app()->createUrl('teacher/classes'); ?>" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Go to Teacher Dashboard
                        </a>
                    <?php elseif (Yii::app()->user->isStudent()): ?>
                        <a href="<?php echo Yii::app()->createUrl('student/dashboard'); ?>" 
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Go to Student Dashboard
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Generic home button -->
                <a href="<?php echo Yii::app()->homeUrl; ?>" 
                   class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Return to Home
                </a>
                
                <!-- Go back button -->
                <button onclick="history.back()" 
                        class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Go Back
                </button>
            </div>
            
            <!-- Additional Help Text -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    If you believe this is a server error, please contact the system administrator.
                </p>
            </div>
        </div>
    </div>
</div>