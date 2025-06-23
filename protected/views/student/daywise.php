<?php
/* @var $this StudentController */
/* @var $student Student */
/* @var $calendarData array */
/* @var $selectedDate string */
/* @var $selectedDateInfo array */
/* @var $currentMonth string */
/* @var $prevMonth string */
/* @var $nextMonth string */
/* @var $startDayOfWeek int */
/* @var $daysInMonth int */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Attendance</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Daily Attendance</h1>
                        <p class="text-blue-100">View your day-by-day attendance records</p>
                    </div>
                    <div>
                        <div class="text-right">
                            <p class="font-semibold"><?php echo CHtml::encode($student->roll_no); ?></p>
                            <p class="text-sm text-blue-100"><?php echo CHtml::encode($student->email); ?></p>
                        </div>
                    </div>
                </div>
                
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Calendar Section -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-700"><?php echo $currentMonth; ?></h2>
                        <div class="flex space-x-2">
                            <a href="<?php echo $this->createUrl('daywise', ['date' => $prevMonth]); ?>" class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <a href="<?php echo $this->createUrl('daywise', ['date' => date('Y-m-d')]); ?>" class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200">Today</a>
                            <a href="<?php echo $this->createUrl('daywise', ['date' => $nextMonth]); ?>" class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1">
                        <!-- Day Headers -->
                        <div class="text-center font-medium text-gray-600 py-2">Sun</div>
                        <div class="text-center font-medium text-gray-600 py-2">Mon</div>
                        <div class="text-center font-medium text-gray-600 py-2">Tue</div>
                        <div class="text-center font-medium text-gray-600 py-2">Wed</div>
                        <div class="text-center font-medium text-gray-600 py-2">Thu</div>
                        <div class="text-center font-medium text-gray-600 py-2">Fri</div>
                        <div class="text-center font-medium text-gray-600 py-2">Sat</div>
                        
                        <!-- Empty cells for days before the 1st of the month -->
                        <?php for ($i = 0; $i < $startDayOfWeek; $i++): ?>
                            <div class="h-14 bg-gray-50"></div>
                        <?php endfor; ?>
                        
                        <!-- Calendar days -->
                        <?php
                        $currentDate = date('Y-m-d');
                        for ($day = 1; $day <= $daysInMonth; $day++): 
                            $date = date('Y-m-d', strtotime(date('Y-m', strtotime($currentMonth)) . '-' . $day));
                            $dayData = isset($calendarData[$date]) ? $calendarData[$date] : null;
                            $isToday = ($date === $currentDate);
                            $isSelected = ($date === $selectedDate);
                            $isWeekend = isset($dayData) && $dayData['status'] === 'weekend';
                            $isFuture = isset($dayData) && $dayData['status'] === 'future';
                            
                            $statusClass = '';
                            $statusBg = '';
                            if (isset($dayData) && $dayData['status'] === 'present') {
                                $statusClass = 'border-green-500';
                                $statusBg = 'bg-green-50';
                            } elseif (isset($dayData) && $dayData['status'] === 'late') {
                                $statusClass = 'border-yellow-500';
                                $statusBg = 'bg-yellow-50';
                            } elseif (isset($dayData) && $dayData['status'] === 'absent') {
                                $statusClass = 'border-red-500';
                                $statusBg = 'bg-red-50';
                            } elseif ($isWeekend) {
                                $statusClass = 'border-gray-300';
                                $statusBg = 'bg-gray-50';
                            } elseif ($isFuture) {
                                $statusClass = 'border-gray-200';
                                $statusBg = 'bg-gray-50';
                            }
                            
                            $cellClass = "h-14 relative flex flex-col cursor-pointer border hover:border-blue-500 " . 
                                         ($isSelected ? 'border-2 border-blue-500 ' : 'border ') .
                                         ($isToday ? 'ring-2 ring-blue-200 ' : '') .
                                         $statusClass;
                                         
                            // Encode day data as JSON for use in JavaScript
                            $dayDataJson = json_encode($dayData);
                        ?>
                            <div onclick="showDayDetails(<?php echo htmlspecialchars($dayDataJson, ENT_QUOTES, 'UTF-8'); ?>)" 
                                class="<?php echo $cellClass; ?> <?php echo $statusBg; ?>">
                                <div class="text-right p-1">
                                    <span class="text-sm <?php echo $isToday ? 'font-bold' : ''; ?>">
                                        <?php echo $day; ?>
                                    </span>
                                </div>
                                
                                <?php if (isset($dayData) && $dayData['status'] !== 'weekend' && $dayData['status'] !== 'future'): ?>
                                    <div class="flex-grow flex items-end p-1">
                                        <div class="w-full flex justify-between text-xs">
                                            <span>
                                                <?php 
                                                    if (isset($dayData['details']['classes'])) {
                                                        echo count($dayData['details']['classes']) . ' class' . 
                                                            (count($dayData['details']['classes']) !== 1 ? 'es' : '');
                                                    }
                                                ?>
                                            </span>
                                            
                                            <?php if ($dayData['status'] === 'present'): ?>
                                                <span class="text-green-600">✓</span>
                                            <?php elseif ($dayData['status'] === 'late'): ?>
                                                <span class="text-yellow-600">⌛</span>
                                            <?php elseif ($dayData['status'] === 'absent'): ?>
                                                <span class="text-red-600">✗</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <div class="mt-4 flex justify-end space-x-6">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 mr-2"></div>
                            <span class="text-sm">Present</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 mr-2"></div>
                            <span class="text-sm">Late</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 mr-2"></div>
                            <span class="text-sm">Absent</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Popup -->
    <div id="dayDetailsModal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto max-h-full">
            <!-- Modal Content -->
            <div class="modal-content py-4 text-left px-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 id="modalDate" class="text-xl font-semibold text-gray-700"></h3>
                    <button class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div id="modalBody" class="my-4">
                    <!-- Content will be populated by JavaScript -->
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end pt-2">
                    <button class="modal-close px-4 bg-blue-500 p-2 rounded-md text-white hover:bg-blue-600">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('dayDetailsModal');
        const modalDate = document.getElementById('modalDate');
        const modalBody = document.getElementById('modalBody');
        const closeButtons = document.querySelectorAll('.modal-close');
        const overlay = document.querySelector('.modal-overlay');
        
        // Close modal when clicking on close button or overlay
        closeButtons.forEach(button => {
            button.addEventListener('click', toggleModal);
        });
        
        overlay.addEventListener('click', toggleModal);
        
        // Toggle modal
        function toggleModal() {
            modal.classList.toggle('opacity-0');
            modal.classList.toggle('pointer-events-none');
            document.body.classList.toggle('modal-active');
        }
        
        // Show day details in modal
        function showDayDetails(dayData) {
            if (!dayData) return;
            
            // Format the date for display
            const dateObj = new Date(dayData.date);
            const formattedDate = dateObj.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            // Set the modal title
            modalDate.textContent = formattedDate;
            
            // Generate modal content based on day status
            let modalContent = '';
            
            if (dayData.status === 'future') {
                modalContent = `
                    <div class="py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500">No attendance data available for this date.</p>
                    </div>
                `;
            } else if (dayData.status === 'weekend') {
                modalContent = `
                    <div class="py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <p class="text-gray-500">Weekend - No Classes</p>
                    </div>
                `;
            } else {
                // Status badge
                let statusBadge = '';
                if (dayData.status === 'present') {
                    statusBadge = `
                        <div class="bg-green-100 text-green-800 px-3 py-2 rounded-md inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Present
                        </div>
                    `;
                } else if (dayData.status === 'late') {
                    statusBadge = `
                        <div class="bg-yellow-100 text-yellow-800 px-3 py-2 rounded-md inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Late
                        </div>
                    `;
                } else if (dayData.status === 'absent') {
                    statusBadge = `
                        <div class="bg-red-100 text-red-800 px-3 py-2 rounded-md inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Absent
                        </div>
                    `;
                }
                
                // Check-in/out times
                let timeInfo = '';
                if (dayData.status !== 'absent' && dayData.details.check_in) {
                    timeInfo = `
                        <div class="mb-6">
                            <h3 class="text-md font-medium text-gray-700 mb-2">Attendance Time</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Check In</p>
                                    <p class="font-medium">${dayData.details.check_in}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Check Out</p>
                                    <p class="font-medium">${dayData.details.check_out || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                // Classes info
                let classesInfo = `
                    <div class="mb-6">
                        <h3 class="text-md font-medium text-gray-700 mb-2">Classes</h3>
                `;
                
                if (!dayData.details.classes || dayData.details.classes.length === 0) {
                    classesInfo += `<p class="text-gray-500 text-sm">No classes scheduled for this day.</p>`;
                } else {
                    classesInfo += `<div class="space-y-3">`;
                    
                    dayData.details.classes.forEach(classItem => {
                        let classStatusClass = '';
                        if (classItem.status === 'present') {
                            classStatusClass = 'border-green-200 bg-green-50';
                        } else if (classItem.status === 'late') {
                            classStatusClass = 'border-yellow-200 bg-yellow-50';
                        } else {
                            classStatusClass = 'border-red-200 bg-red-50';
                        }
                        
                        let statusBadgeClass = '';
                        if (classItem.status === 'present') {
                            statusBadgeClass = 'bg-green-200 text-green-800';
                        } else if (classItem.status === 'late') {
                            statusBadgeClass = 'bg-yellow-200 text-yellow-800';
                        } else {
                            statusBadgeClass = 'bg-red-200 text-red-800';
                        }
                        
                        classesInfo += `
                            <div class="border rounded-md p-3 ${classStatusClass}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium">${classItem.name}</h4>
                                        <p class="text-sm text-gray-600">${classItem.time}</p>
                                    </div>
                                    <span class="text-xs font-medium px-2 py-1 rounded ${statusBadgeClass}">
                                        ${classItem.status.charAt(0).toUpperCase() + classItem.status.slice(1)}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Instructor: ${classItem.instructor}</p>
                            </div>
                        `;
                    });
                    
                    classesInfo += `</div>`;
                }
                
                classesInfo += `</div>`;
                
                // Notes
                let notesInfo = '';
                if (dayData.details.notes && dayData.details.notes.length > 0) {
                    notesInfo = `
                        <div>
                            <h3 class="text-md font-medium text-gray-700 mb-2">Notes</h3>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    `;
                    
                    dayData.details.notes.forEach(note => {
                        notesInfo += `<li>${note}</li>`;
                    });
                    
                    notesInfo += `
                            </ul>
                        </div>
                    `;
                }
                
                // Combine all sections
                modalContent = `
                    <div class="mb-6">
                        ${statusBadge}
                    </div>
                    ${timeInfo}
                    ${classesInfo}
                    ${notesInfo}
                `;
            }
            
            // Set the modal content
            modalBody.innerHTML = modalContent;
            
            // Show the modal
            toggleModal();
        }
    </script>
</body>
</html>
