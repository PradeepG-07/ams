<?php if ($attendanceDataProvider !== []): ?>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Attendance Records</h3>
            <?php if ($fromDate && $toDate): ?>
                <p class="text-sm text-gray-600 mt-1">
                    Showing records from <span class="font-medium"><?php echo $fromDate; ?></span> 
                    to <span class="font-medium"><?php echo $toDate; ?></span>
                </p>
            <?php endif; ?>
        </div>
        
        <?php if ($attendanceDataProvider->getTotalItemCount() == 0): ?>
            <div class="px-6 py-8 text-center">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <svg class="mx-auto h-12 w-12 text-blue-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-blue-900 mb-2">No Records Found</h3>
                    <p class="text-blue-700">No attendance records found for the selected date range.</p>
                </div>
            </div>
        <?php else: ?>
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'attendance-grid',
                'dataProvider' => $attendanceDataProvider,
                'htmlOptions' => array('class' => 'w-full'),
                'itemsCssClass' => 'min-w-full divide-y divide-gray-200',
                'pagerCssClass' => 'px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-between items-center',
                'summaryCssClass' => 'px-6 py-3 bg-gray-50 text-sm text-gray-700',
                'enablePagination' => true,
                'enableSorting' => true,
                'pager' => array(
                    'class' => 'CLinkPager',
                    'header' => '',
                    'firstPageLabel' => '« First',
                    'lastPageLabel' => 'Last »',    
                    'prevPageLabel' => 'Previous',
                    'nextPageLabel' => 'Next',
                    'maxButtonCount' => 5,
                ),
                'columns' => array(
                    array(
                        'name' => 'date',
                        'header' => 'Date',
                        'headerHtmlOptions' => array('class' => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'),
                        'htmlOptions' => array('class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'),
                        'value' => function($data) {
                            if (isset($data['date']) && $data['date'] instanceof MongoDate) {
                                return date('F j, Y', $data['date']->sec);
                            }
                            return 'N/A';
                        },
                        'type' => 'raw',
                    ),
                    array(
                        'name' => 'day',
                        'header' => 'Day',
                        'headerHtmlOptions' => array('class' => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'),
                        'htmlOptions' => array('class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'),
                        'value' => function($data) {
                            if (isset($data['date']) && $data['date'] instanceof MongoDate) {
                                return date('l', $data['date']->sec);
                            }
                            return 'N/A';
                        },
                        'type' => 'raw',
                    ),
                    array(
                        'name' => 'status',
                        'header' => 'Attendance Status',
                        'headerHtmlOptions' => array('class' => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'),
                        'htmlOptions' => array('class' => 'px-6 py-4 whitespace-nowrap'),
                        'value' => function($data) {
                            $studentId = new MongoDB\BSON\ObjectId(Yii::app()->user->getState('student_id'));
                            $isPresent = false;
                            
                            // if the student ID is in the student_ids array
                            if (isset($data['student_ids']) && is_array($data['student_ids'])) {
                                foreach ($data['student_ids'] as $id) {
                                    if ($id instanceof MongoDB\BSON\ObjectId && $id == $studentId) {
                                        $isPresent = true;
                                        break;
                                    }
                                }
                            }
                            
                            if ($isPresent) {
                                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Present</span>';
                            } else {
                                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Absent</span>';
                            }
                        },
                        'type' => 'raw',
                    ),
                    array(
                        'name' => 'class_name',
                        'header' => 'Class',
                        'headerHtmlOptions' => array('class' => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'),
                        'htmlOptions' => array('class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'),
                        'value' => function($data) {
                            return !empty(Yii::app()->user->getState('studentClass')) ? Yii::app()->user->getState('studentClass') : 'N/A';
                        },
                        'type' => 'raw',
                    ),
                ),
            )); ?>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
        <p class="text-blue-700">Please select a date range to view attendance records.</p>
    </div>
<?php endif; ?>