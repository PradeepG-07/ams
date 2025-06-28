<?php
/* @var $this StudentController */
/* @var $attendanceDataProvider CArrayDataProvider */
/* @var $fromDate string */
/* @var $toDate string */
?>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Attendance Range</h1>

        <!-- Flash Messages Container -->
        <div id="flash-messages" class="mb-6">
            <!-- Flash messages will be inserted here -->
        </div>

        <!-- Date Filter Form -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Filter by Date Range</h2>
            
            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'attendance-range-form',
                'enableAjaxValidation' => false,
                'htmlOptions' => array('class' => 'space-y-4')
            )); ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="fromDate" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <?php echo CHtml::dateField('fromDate', $fromDate, array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                        'placeholder' => 'Select start date',
                        'type' => 'date',
                        'max' => date('Y-m-d')
                    )); ?>
                </div>
                
                <div>
                    <label for="toDate" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <?php echo CHtml::dateField('toDate', $toDate, array(
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                        'placeholder' => 'Select end date',
                        'type' => 'date',
                        'max' => date('Y-m-d')
                    )); ?>
                </div>
                
                <div>
                    <?php echo CHtml::button('Filter', array(
                        'id' => 'filter-btn',
                        'class' => 'w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                    )); ?>
                </div>
            </div>

            <?php $this->endWidget(); ?>
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="hidden text-center py-8">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm text-blue-600">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading attendance records...
            </div>
        </div>

        <!-- Attendance Results Container -->
        <div id="attendance-results">
            <?php $this->renderPartial('attendancegrid', array(
                'attendanceDataProvider' => $attendanceDataProvider,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            )); ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Function to show flash message
    function showFlashMessage(message, type = 'error') {
        const alertClass = type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-blue-50 border-blue-200 text-blue-800';
        const iconPath = type === 'error' ? 
            'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' :
            'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        
        const flashHtml = `
            <div class="rounded-md ${alertClass} p-4 border">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="${iconPath}" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" class="close-flash inline-flex rounded-md p-1.5 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#flash-messages').html(flashHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#flash-messages').fadeOut(500, function() {
                $(this).empty().show();
            });
        }, 5000);
    }
    
    // Close flash message handler
    $(document).on('click', '.close-flash', function() {
        $('#flash-messages').fadeOut(500, function() {
            $(this).empty().show();
        });
    });
    
    $('#filter-btn').on('click', function(e) {
        e.preventDefault();
        
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        
        // Clear any existing flash messages
        $('#flash-messages').empty();
        
        if (!fromDate || !toDate) {
            showFlashMessage('Please select both from and to dates.', 'error');
            return;
        }
        
        if (new Date(fromDate) > new Date(toDate)) {
            showFlashMessage('From date cannot be later than to date.', 'error');
            return;
        }
        
        // Check if student is assigned to a class
        <?php if (!Yii::app()->user->getState('studentClassId')): ?>
        showFlashMessage('You are not assigned to any class. Please contact your administrator.', 'error');
        return;
        <?php endif; ?>
        
        // Show loading indicator
        $('#loading-indicator').removeClass('hidden');
        $('#attendance-results').addClass('opacity-50');
        
        $.ajax({
            url: '<?php echo Yii::app()->createUrl('student/attendancerange') ?>',
            type: 'GET',
            data: {
                fromDate: fromDate,
                toDate: toDate
            },
            success: function(response) {
                $('#attendance-results').html(response);
                $('#loading-indicator').addClass('hidden');
                $('#attendance-results').removeClass('opacity-50');
                showFlashMessage('Attendance records loaded successfully.', 'success');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showFlashMessage('An error occurred while loading attendance records. Please try again.', 'error');
                $('#loading-indicator').addClass('hidden');
                $('#attendance-results').removeClass('opacity-50');
            }
        });
    });
});
</script>