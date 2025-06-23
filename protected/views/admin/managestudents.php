<?php
/* @var $this AdminController */
/* @var $students array */
/* @var $totalStudents int */
/* @var $activeStudents int */
/* @var $inactiveStudents int */
/* @var $attendanceStats array */
/* @var $attendanceTrendData array */
?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - AMS Admin</title>
    <!-- FIX: Replaced the broken/outdated Tailwind CSS CDN link with the modern, functional Play CDN. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Student Management</h1>
                        <p class="text-blue-100">Attendance Management System</p>
                    </div>
                    <div>
                        <button id="addStudentBtn"
                            class="bg-white text-blue-600 px-4 py-2 rounded-md font-medium hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add New Student
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Statistics Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Students Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-700" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Students</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $totalStudents; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Active Students Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-700" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Active Students</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $activeStudents; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Inactive Students Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-700" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Inactive Students</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $inactiveStudents; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Distribution Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-purple-700" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Attendance Distribution</p>
                            <div class="mt-1 flex space-x-1">
                                <?php if ($totalStudents > 0): ?>
                                    <div class="h-3 bg-green-500 rounded-l-sm"
                                        style="width: <?php echo ($attendanceStats['excellent'] / $totalStudents) * 100; ?>px"
                                        title="Excellent: <?php echo $attendanceStats['excellent']; ?>"></div>
                                    <div class="h-3 bg-blue-500"
                                        style="width: <?php echo ($attendanceStats['good'] / $totalStudents) * 100; ?>px"
                                        title="Good: <?php echo $attendanceStats['good']; ?>"></div>
                                    <div class="h-3 bg-yellow-500"
                                        style="width: <?php echo ($attendanceStats['average'] / $totalStudents) * 100; ?>px"
                                        title="Average: <?php echo $attendanceStats['average']; ?>"></div>
                                    <div class="h-3 bg-red-500 rounded-r-sm"
                                        style="width: <?php echo ($attendanceStats['poor'] / $totalStudents) * 100; ?>px"
                                        title="Poor: <?php echo $attendanceStats['poor']; ?>"></div>
                                <?php else: ?>
                                    <div class="text-gray-400 text-xs">No student data available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Attendance Distribution</h2>
                    <div class="h-64">
                        <canvas id="attendanceDistributionChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Monthly Attendance Trend</h2>
                    <div class="h-64">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="w-full md:w-1/3">
                        <div class="relative">
                            <input id="searchInput" type="text" placeholder="Search students..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="attendanceFilter"
                                class="block text-sm font-medium text-gray-700 mb-1">Attendance</label>
                            <select id="attendanceFilter"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All</option>
                                <option value="excellent">Excellent (>90%)</option>
                                <option value="good">Good (80-90%)</option>
                                <option value="average">Average (70-80%)</option>
                                <option value="poor">Poor (<70%)</option>
                            </select>
                        </div>

                        <div>
                            <label for="statusFilter"
                                class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="statusFilter"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <button id="exportBtn"
                            class="mt-6 flex items-center text-sm bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700">Student List</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Roll No
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Classes
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Attendance %
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($totalStudents > 0): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr class="hover:bg-gray-50" data-id="<?php echo $student->_id; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-800 font-semibold">
                                                            <?php echo substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo CHtml::encode($student->first_name . ' ' . $student->last_name); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo CHtml::encode($student->roll_no); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo CHtml::encode($student->email); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <?php if (!empty($student->classes)): ?>
                                                    <div class="flex flex-wrap gap-2">
                                                        <?php foreach (array_slice(array_keys($student->classes), 0, 2) as $index => $class): ?>
                                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                                <?php echo CHtml::encode($class); ?>
                                                            </span>
                                                        <?php endforeach; ?>

                                                        <?php if (count($student->classes) > 2): ?>
                                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                                +<?php echo count($student->classes) - 2; ?> more
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-gray-400">No classes</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $attendanceColor = 'text-red-600';
                                            if ($student->percentage >= 90)
                                                $attendanceColor = 'text-green-600';
                                            else if ($student->percentage >= 80)
                                                $attendanceColor = 'text-blue-600';
                                            else if ($student->percentage >= 70)
                                                $attendanceColor = 'text-yellow-600';
                                            ?>
                                            <div class="text-sm font-medium <?php echo $attendanceColor; ?>">
                                                <?php echo $student->percentage; ?>%
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full <?php echo !empty($student->classes) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo !empty($student->classes) ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <button data-id="<?php echo $student->_id; ?>"
                                                    class="view-student text-blue-600 hover:text-blue-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button data-id="<?php echo $student->_id; ?>"
                                                    class="edit-student text-indigo-600 hover:text-indigo-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button data-id="<?php echo $student->_id; ?>"
                                                    class="delete-student text-red-600 hover:text-red-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No students found. Add students using the button above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <?php if ($totalStudents > 0): ?>
                                    <span class="font-medium">1</span> to <span
                                        class="font-medium"><?php echo min(10, $totalStudents); ?></span> of <span
                                        class="font-medium"><?php echo $totalStudents; ?></span>
                                <?php else: ?>
                                    <span class="font-medium">0</span> of <span class="font-medium">0</span>
                                <?php endif; ?>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination">
                                <a href="#"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" aria-current="page"
                                    class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    1
                                </a>
                                <a href="#"
                                    class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    2
                                </a>
                                <a href="#"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Student Modal -->
    <div id="studentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800">Add New Student</h2>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Form success/error message -->
            <div id="formFeedback" class="mb-4 hidden">
                <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 hidden">
                </div>
                <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 hidden"></div>
            </div>

            <form id="studentForm">
                <input type="hidden" id="studentId" name="studentId" value="">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="firstName" name="Student[first_name]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="lastName" name="Student[last_name]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="Student[email]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label for="rollNo" class="block text-sm font-medium text-gray-700 mb-1">Roll No</label>
                        <input type="text" id="rollNo" name="Student[roll_no]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>

                    <div id="passwordField">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="Student[password]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">Attendance
                            Percentage</label>
                        <input type="number" id="percentage" name="Student[percentage]" min="0" max="100"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Classes Section -->
                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Class Assignment</h3>
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="relative w-full">
                            <select id="classroomDropdown"
                                class="appearance-none w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a Class</option>
                                <!-- Options will be loaded via JavaScript -->
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <button type="button" id="assignClassBtn"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                            Add Class
                        </button>
                        <a href="<?php echo Yii::app()->createUrl('admin/manageclasses'); ?>"
                            class="px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Class
                        </a>
                    </div>

                    <!-- Assigned Classes List -->
                    <div id="assignedClassesContainer" class="mt-2">
                        <div id="noClassesMessage" class="text-gray-500 text-sm">No classes assigned</div>
                        <div id="assignedClasses" class="flex flex-wrap gap-2">
                            <!-- Assigned classes will be added here via JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Address Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="addressLine1" class="block text-sm font-medium text-gray-700 mb-1">Address Line
                                1</label>
                            <input type="text" id="addressLine1" name="Address[address_line1]"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="addressLine2" class="block text-sm font-medium text-gray-700 mb-1">Address Line
                                2</label>
                            <input type="text" id="addressLine2" name="Address[address_line2]"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" id="city" name="Address[city]"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" id="state" name="Address[state]"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="zip" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                            <input type="text" id="zip" name="Address[zip]"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" id="country" name="Address[country]"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" id="cancelForm"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="saveButton"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span>Save Student</span>
                        <span id="saveSpinner" class="hidden ml-2 animate-spin">↻</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Student Modal -->
    <div id="viewStudentModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="viewModalTitle" class="text-xl font-bold text-gray-800">Student Details</h2>
                <button id="closeViewModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="studentDetails" class="mb-6">
                <div class="flex items-center mb-4">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span id="studentInitials" class="text-blue-800 text-2xl font-semibold"></span>
                    </div>
                    <div class="ml-4">
                        <h3 id="studentName" class="text-lg font-semibold text-gray-800"></h3>
                        <p id="studentEmail" class="text-gray-600"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Roll No</h4>
                        <p id="viewRollNo" class="font-medium"></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Attendance</h4>
                        <p id="viewAttendance" class="font-medium"></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Status</h4>
                        <p id="viewStatus" class="font-medium"></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Classes</h4>
                        <div id="viewClasses"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Address Information</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p id="viewAddress" class="text-gray-700"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button id="closeViewBtn"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h2>
                <p class="text-gray-600">Are you sure you want to delete this student? This action cannot be undone.</p>
            </div>

            <div class="flex justify-end space-x-2">
                <button id="cancelDelete"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmDelete"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize charts
            const attendanceDistributionCtx = document.getElementById('attendanceDistributionChart').getContext('2d');
            new Chart(attendanceDistributionCtx, {
                type: 'pie',
                data: {
                    labels: ['Excellent (>90%)', 'Good (80-90%)', 'Average (70-80%)', 'Poor (<70%)'],
                    datasets: [{
                        data: [
                            <?php echo $attendanceStats['excellent']; ?>,
                            <?php echo $attendanceStats['good']; ?>,
                            <?php echo $attendanceStats['average']; ?>,
                            <?php echo $attendanceStats['poor']; ?>
                        ],
                        backgroundColor: [
                            'rgba(52, 211, 153, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Prepare attendance trend data from PHP
            // FIX: Added null coalescing operator (?? []) to ensure attendanceTrendData is always an array.
            // This prevents a JavaScript error if the controller doesn't pass the variable.
            const attendanceTrendData = <?php echo json_encode($attendanceTrendData ?? []); ?>;
            const trendLabels = attendanceTrendData.map(item => item.label);
            const trendPercentages = attendanceTrendData.map(item => item.percentage);

            const attendanceTrendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
            new Chart(attendanceTrendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Daily Attendance Rate %',
                        data: trendPercentages,
                        borderColor: 'rgba(59, 130, 246, 0.8)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Last 30 Days Attendance Trend'
                        },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const dataIndex = context.dataIndex;
                                    const data = attendanceTrendData[dataIndex];
                                    if (!data) return '';
                                    return `Present: ${data.present} / ${data.total} students`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Attendance Percentage (%)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Variables for modals
            const studentModal = document.getElementById('studentModal');
            const viewStudentModal = document.getElementById('viewStudentModal');
            const deleteModal = document.getElementById('deleteModal');
            const studentForm = document.getElementById('studentForm');
            const formFeedback = document.getElementById('formFeedback');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const saveButton = document.getElementById('saveButton');
            const saveSpinner = document.getElementById('saveSpinner');

            // Show add student modal
            document.getElementById('addStudentBtn').addEventListener('click', function () {
                resetFormMessages();
                document.getElementById('modalTitle').textContent = 'Add New Student';
                document.getElementById('studentId').value = '';
                document.getElementById('studentForm').reset();
                document.getElementById('passwordField').style.display = 'block'; // Show password field for new students

                studentModal.classList.remove('hidden');
            });

            // Reset form messages
            function resetFormMessages() {
                formFeedback.classList.add('hidden');
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');
                successMessage.textContent = '';
                errorMessage.textContent = '';
            }

            // Display form message
            function displayFormMessage(isSuccess, message) {
                formFeedback.classList.remove('hidden');

                if (isSuccess) {
                    successMessage.innerHTML = message;
                    successMessage.classList.remove('hidden');
                    errorMessage.classList.add('hidden');
                } else {
                    errorMessage.innerHTML = message;
                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');
                }
            }

            // Edit student buttons
            document.querySelectorAll('.edit-student').forEach(button => {
                button.addEventListener('click', function () {
                    resetFormMessages();
                    const studentId = this.getAttribute('data-id');
                    document.getElementById('modalTitle').textContent = 'Edit Student';
                    document.getElementById('studentId').value = studentId;
                    document.getElementById('passwordField').style.display = 'none'; // Hide password field when editing

                    // Show loading in the form
                    saveButton.disabled = true;
                    saveSpinner.classList.remove('hidden');

                    // Fetch the student data
                    fetch(`<?php echo Yii::app()->createUrl('admin/getStudent'); ?>?id=${studentId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                populateStudentForm(data.student);
                            } else {
                                displayFormMessage(false, data.message || 'Failed to load student data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching student data:', error);
                            displayFormMessage(false, 'An error occurred while loading student data.');
                        })
                        .finally(() => {
                            saveButton.disabled = false;
                            saveSpinner.classList.add('hidden');
                        });

                    studentModal.classList.remove('hidden');
                });
            });

            // View student buttons
            document.querySelectorAll('.view-student').forEach(button => {
                button.addEventListener('click', function () {
                    const studentId = this.getAttribute('data-id');

                    // Fetch the student data
                    fetch(`<?php echo Yii::app()->createUrl('admin/getStudent'); ?>?id=${studentId}`)
                        .then(response => {
                           if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                populateStudentView(data.student);
                                viewStudentModal.classList.remove('hidden');
                            } else {
                                alert(data.message || 'Failed to load student data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching student data for view:', error);
                            alert('An error occurred while loading student data.');
                        });
                });
            });

            // Delete student buttons
            document.querySelectorAll('.delete-student').forEach(button => {
                button.addEventListener('click', function () {
                    const studentId = this.getAttribute('data-id');
                    document.getElementById('confirmDelete').setAttribute('data-id', studentId);
                    deleteModal.classList.remove('hidden');
                });
            });

            // Close modals
            document.getElementById('closeModal').addEventListener('click', function () {
                studentModal.classList.add('hidden');
            });

            document.getElementById('cancelForm').addEventListener('click', function () {
                studentModal.classList.add('hidden');
            });

            document.getElementById('closeViewModal').addEventListener('click', function () {
                viewStudentModal.classList.add('hidden');
            });

            document.getElementById('closeViewBtn').addEventListener('click', function () {
                viewStudentModal.classList.add('hidden');
            });

            document.getElementById('cancelDelete').addEventListener('click', function () {
                deleteModal.classList.add('hidden');
            });

            // Edit from view

            // Submit form
            studentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                resetFormMessages();

                const studentId = document.getElementById('studentId').value;
                const formData = new FormData(this);
                const isNewStudent = !studentId;

                // Disable submit button and show spinner
                saveButton.disabled = true;
                saveSpinner.classList.remove('hidden');

                // Determine the URL based on create or update
                const url = isNewStudent
                    ? '<?php echo Yii::app()->createUrl('admin/createStudent'); ?>'
                    : `<?php echo Yii::app()->createUrl('admin/updateStudent'); ?>?id=${studentId}`;

                // Send AJAX request
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayFormMessage(true, isNewStudent ? 'Student created successfully!' : 'Student updated successfully!');

                            // Reload page after a short delay
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            let errorMsg = data.message || 'An error occurred.';

                            if (data.errors) {
                                errorMsg += '<ul class="list-disc pl-5 mt-2">';
                                for (const field in data.errors) {
                                    for (const error of data.errors[field]) {
                                        errorMsg += `<li>${error}</li>`;
                                    }
                                }
                                errorMsg += '</ul>';
                            }

                            displayFormMessage(false, errorMsg);
                            saveButton.disabled = false;
                            saveSpinner.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting form:', error);
                        displayFormMessage(false, 'A network error occurred. Please try again.');
                        saveButton.disabled = false;
                        saveSpinner.classList.add('hidden');
                    });
            });

            // Confirm delete
            document.getElementById('confirmDelete').addEventListener('click', function () {
                const studentId = this.getAttribute('data-id');
                const button = this;

                // Disable button to prevent multiple clicks
                button.disabled = true;
                button.textContent = 'Deleting...';

                // Send delete request
                fetch(`<?php echo Yii::app()->createUrl('admin/deleteStudent'); ?>?id=${studentId}`, {
                    method: 'POST'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            deleteModal.classList.add('hidden');

                            // Remove the row or reload the page
                            const row = document.querySelector(`tr[data-id="${studentId}"]`);
                            if (row) {
                                row.remove();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert(data.message || 'Failed to delete student');
                            button.disabled = false;
                            button.textContent = 'Delete';
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting student:', error);
                        alert('An error occurred while deleting the student.');
                        button.disabled = false;
                        button.textContent = 'Delete';
                    });
            });

            // Populate student form
            function populateStudentForm(student) {
                if (!student) {
                    console.error('No student data provided');
                    return;
                }

                document.getElementById('firstName').value = student.first_name || '';
                document.getElementById('lastName').value = student.last_name || '';
                document.getElementById('email').value = student.email || '';
                document.getElementById('rollNo').value = student.roll_no || '';
                document.getElementById('percentage').value = student.percentage || '';

                // Address
                if (student.address) {
                    document.getElementById('addressLine1').value = student.address.address_line1 || '';
                    document.getElementById('addressLine2').value = student.address.address_line2 || '';
                    document.getElementById('city').value = student.address.city || '';
                    document.getElementById('state').value = student.address.state || '';
                    document.getElementById('zip').value = student.address.zip || '';
                    document.getElementById('country').value = student.address.country || '';
                }
            }

            // Populate student view
            function populateStudentView(student) {
                if (!student) {
                    console.error('No student data provided');
                    return;
                }

                // Set student details
                const initials = (student.first_name?.[0] || '') + (student.last_name?.[0] || '');
                document.getElementById('studentInitials').textContent = initials.toUpperCase();
                document.getElementById('studentInitials').setAttribute('data-id', student._id);
                document.getElementById('studentName').textContent = (student.first_name || '') + ' ' + (student.last_name || '');
                document.getElementById('studentEmail').textContent = student.email || '';
                document.getElementById('viewRollNo').textContent = student.roll_no || 'N/A';

                // Attendance
                const attendanceEl = document.getElementById('viewAttendance');
                attendanceEl.textContent = (student.percentage || '0') + '%';
                let colorClass = 'text-red-600';
                if (student.percentage >= 90) colorClass = 'text-green-600';
                else if (student.percentage >= 80) colorClass = 'text-blue-600';
                else if (student.percentage >= 70) colorClass = 'text-yellow-600';
                attendanceEl.className = 'font-medium ' + colorClass;

                // Status
                const statusEl = document.getElementById('viewStatus');
                const hasClasses = student.classes && Object.keys(student.classes).length > 0;
                statusEl.innerHTML = `<span class="px-2 py-1 text-xs rounded-full ${hasClasses ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${hasClasses ? 'Active' : 'Inactive'}</span>`;

                // Classes
                const classesEl = document.getElementById('viewClasses');
                classesEl.innerHTML = '';
                if (student.classes && Object.keys(student.classes).length > 0) {
                    const classesDiv = document.createElement('div');
                    classesDiv.className = 'flex flex-wrap gap-2';

                    for (const className in student.classes) {
                        const classData = student.classes[className];
                        const classSpan = document.createElement('span');
                        classSpan.className = 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800';
                        classSpan.textContent = `${className} - ${classData.subject}`;
                        classesDiv.appendChild(classSpan);
                    }

                    classesEl.appendChild(classesDiv);
                } else {
                    classesEl.textContent = 'No classes enrolled';
                }

                // Address
                let addressText = 'No address information';
                if (student.address) {
                    const addressParts = [
                        student.address.address_line1,
                        student.address.address_line2,
                        student.address.city,
                        student.address.state,
                        student.address.zip,
                        student.address.country
                    ].filter(Boolean);

                    if (addressParts.length > 0) {
                        addressText = addressParts.join(', ');
                    }
                }
                document.getElementById('viewAddress').textContent = addressText;
            }

            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function () {
                const searchValue = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(row => {
                    // Skip "No students found" row
                    if (row.cells.length === 1) return;

                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                });
            });

            // Filter functionality
            document.getElementById('attendanceFilter').addEventListener('change', function () {
                applyFilters();
            });

            document.getElementById('statusFilter').addEventListener('change', function () {
                applyFilters();
            });

            function applyFilters() {
                const attendanceFilter = document.getElementById('attendanceFilter').value;
                const statusFilter = document.getElementById('statusFilter').value;
                const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(row => {
                    // Skip "No students found" row
                    if (row.cells.length === 1) return;

                    let showRow = true;

                    // Apply attendance filter
                    if (attendanceFilter) {
                        const attendanceCell = row.querySelector('td:nth-child(5)');
                        if (attendanceCell) {
                            const attendance = parseInt(attendanceCell.textContent);

                            if (attendanceFilter === 'excellent' && attendance < 90) showRow = false;
                            else if (attendanceFilter === 'good' && (attendance < 80 || attendance >= 90)) showRow = false;
                            else if (attendanceFilter === 'average' && (attendance < 70 || attendance >= 80)) showRow = false;
                            else if (attendanceFilter === 'poor' && attendance >= 70) showRow = false;
                        }
                    }

                    // Apply status filter
                    if (statusFilter && showRow) {
                        const statusCell = row.querySelector('td:nth-child(6)');
                        if (statusCell) {
                            const status = statusCell.textContent.trim();

                            if (statusFilter === 'active' && status !== 'Active') showRow = false;
                            else if (statusFilter === 'inactive' && status !== 'Inactive') showRow = false;
                        }
                    }

                    row.style.display = showRow ? '' : 'none';
                });
            }

            // Export button
            document.getElementById('exportBtn').addEventListener('click', function () {
                alert('The export functionality would generate a CSV or Excel file with student data.');
            });

            // Variables for class assignment
            const classroomDropdown = document.getElementById('classroomDropdown');
            const assignClassBtn = document.getElementById('assignClassBtn');
            const assignedClassesContainer = document.getElementById('assignedClassesContainer');
            const assignedClasses = document.getElementById('assignedClasses');
            const noClassesMessage = document.getElementById('noClassesMessage');
            let studentClasses = {}; // Store assigned classes

            // Populate class dropdown
            function loadClassrooms() {
                fetch('<?php echo Yii::app()->createUrl('admin/getClassrooms'); ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear existing options
                            classroomDropdown.innerHTML = '<option value="">Select a Class</option>';

                            // Add classroom options
                            data.classrooms.forEach(classroom => {
                                const option = document.createElement('option');
                                option.value = classroom.id;
                                option.textContent = `${classroom.name} - ${classroom.subject} (${classroom.academicYear})`;
                                option.dataset.name = classroom.name;
                                option.dataset.subject = classroom.subject;
                                option.dataset.academicYear = classroom.academicYear;
                                classroomDropdown.appendChild(option);
                            });
                        } else {
                            console.error('Error loading classrooms:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching classrooms:', error);
                    });
            }

            // Assign class button click handler
            assignClassBtn.addEventListener('click', function () {
                const selectedClassroom = classroomDropdown.value;
                if (!selectedClassroom) {
                    alert('Please select a classroom to assign');
                    return;
                }

                const studentId = document.getElementById('studentId').value;
                if (!studentId) {
                    alert('Please save the student first to assign classes');
                    return;
                }

                // Disable the button during the request
                assignClassBtn.disabled = true;

                // Send the request to assign the class
                const formData = new FormData();
                formData.append('studentId', studentId);
                formData.append('classroomId', selectedClassroom);

                fetch('<?php echo Yii::app()->createUrl('admin/assignClassToStudent'); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Add the class to the UI
                            const classroom = data.classroom;
                            addClassToUI(classroom.name, classroom.subject, classroom.academic_year);

                            // Store in local student classes
                            studentClasses[classroom.name] = {
                                id: classroom.id,
                                subject: classroom.subject,
                                academic_year: classroom.academic_year
                            };

                            // Reset dropdown
                            classroomDropdown.value = '';
                        } else {
                            alert(data.message || 'Failed to assign class');
                        }
                        assignClassBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error assigning class:', error);
                        alert('An error occurred while assigning the class');
                        assignClassBtn.disabled = false;
                    });
            });

            // Function to add a class to the UI
            function addClassToUI(className, subject, academicYear) {
                // Hide the "no classes" message
                noClassesMessage.style.display = 'none';

                // Create class badge
                const classBadge = document.createElement('div');
                classBadge.className = 'flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full';
                classBadge.dataset.className = className;

                // Class info
                const classInfo = document.createElement('span');
                classInfo.textContent = `${className} - ${subject}`;
                classBadge.appendChild(classInfo);

                // Remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'ml-2 text-blue-600 hover:text-blue-800 focus:outline-none';
                removeBtn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                removeBtn.addEventListener('click', function () {
                    removeClassFromStudent(className);
                });

                classBadge.appendChild(removeBtn);
                assignedClasses.appendChild(classBadge);
            }

            // Function to remove a class from a student
            function removeClassFromStudent(className) {
                const studentId = document.getElementById('studentId').value;
                if (!studentId) {
                    // Just remove from UI if student not saved yet
                    const classBadge = document.querySelector(`[data-class-name="${className}"]`);
                    if (classBadge) {
                        classBadge.remove();
                    }
                    delete studentClasses[className];

                    // Show "no classes" message if no classes left
                    if (Object.keys(studentClasses).length === 0) {
                        noClassesMessage.style.display = 'block';
                    }

                    return;
                }

                // Send request to remove the class
                const formData = new FormData();
                formData.append('studentId', studentId);
                formData.append('className', className);

                fetch('<?php echo Yii::app()->createUrl('admin/removeClassFromStudent'); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove from UI
                            const classBadge = document.querySelector(`[data-class-name="${className}"]`);
                            if (classBadge) {
                                classBadge.remove();
                            }

                            // Remove from local store
                            delete studentClasses[className];

                            // Show "no classes" message if no classes left
                            if (Object.keys(studentClasses).length === 0) {
                                noClassesMessage.style.display = 'block';
                            }
                        } else {
                            alert(data.message || 'Failed to remove class');
                        }
                    })
                    .catch(error => {
                        console.error('Error removing class:', error);
                        alert('An error occurred while removing the class');
                    });
            }

            // Extend the populate student form function to include classes
            const originalPopulateStudentForm = populateStudentForm;
            populateStudentForm = function (student) {
                // Call the original function first
                originalPopulateStudentForm(student);

                // Clear existing classes
                assignedClasses.innerHTML = '';
                studentClasses = {};

                // Add classes if any
                if (student.classes && Object.keys(student.classes).length > 0) {
                    noClassesMessage.style.display = 'none';

                    // Store classes in local variable and add to UI
                    for (const className in student.classes) {
                        const classData = student.classes[className];
                        studentClasses[className] = classData;
                        addClassToUI(className, classData.subject, classData.academic_year);
                    }
                } else {
                    noClassesMessage.style.display = 'block';
                }
            };

            // Extend the populateStudentView function to better display classes
            const originalPopulateStudentView = populateStudentView;
            populateStudentView = function (student) {
                // Call the original function first
                originalPopulateStudentView(student);

                // Enhance the classes display in view mode
                const classesEl = document.getElementById('viewClasses');
                classesEl.innerHTML = '';

                if (student.classes && Object.keys(student.classes).length > 0) {
                    const classesDiv = document.createElement('div');
                    classesDiv.className = 'flex flex-wrap gap-2';

                    for (const className in student.classes) {
                        const classData = student.classes[className];
                        const classSpan = document.createElement('span');
                        classSpan.className = 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800';
                        classSpan.textContent = `${className} - ${classData.subject}`;
                        classesDiv.appendChild(classSpan);
                    }

                    classesEl.appendChild(classesDiv);
                } else {
                    classesEl.textContent = 'No classes enrolled';
                }
            };

            // Load classrooms when the add/edit student modal opens
            document.getElementById('addStudentBtn').addEventListener('click', loadClassrooms);
            document.querySelectorAll('.edit-student').forEach(button => {
                button.addEventListener('click', loadClassrooms);
            });
        });
    </script>
</body>