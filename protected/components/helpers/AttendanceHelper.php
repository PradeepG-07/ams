<?php

/**
 * AttendanceHelper - Handles all attendance-related business logic
 * 
 * This class encapsulates attendance operations including:
 * - Attendance calculation and statistics
 * - Data validation and processing
 * - Database operations for attendance records
 * - Error handling and logging
 */
class AttendanceHelper extends CComponent
{
    /**
     * @var array Standard response structure
     */
    private static $responseTemplate = [
        'success' => false,
        'data' => null,
        'message' => '',
        'errors' => []
    ];

    /**
     * Calculate comprehensive attendance data for a student
     * 
     * @param Student $student The student model
     * @return array Standardized response with attendance data
     */
    public static function calculateStudentAttendanceData($student)
    {
        try {
            if (!$student) {
                return self::createErrorResponse('Student not found', 404);
            }

            $attendanceData = [
                'overall' => $student->percentage ?? 0,
                'bySubject' => [],
                'history' => [],
                'statistics' => [
                    'totalClasses' => 0,
                    'totalPresent' => 0,
                    'totalAbsent' => 0,
                    'totalLate' => 0
                ]
            ];

            // Initialize subject attendance tracking
            if (!empty($student->classes)) {
                foreach ($student->classes as $className => $classInfo) {
                    $attendanceData['bySubject'][$className] = 0;
                }
            }

            // Process attendance history if available
            if (!empty($student->attendance)) {
                $attendanceData = self::processAttendanceHistory($student->attendance, $attendanceData);
            }

            // Calculate overall percentage if we have records
            if ($attendanceData['statistics']['totalClasses'] > 0) {
                $attendanceData['overall'] = round(
                    ($attendanceData['statistics']['totalPresent'] / $attendanceData['statistics']['totalClasses']) * 100, 
                    1
                );
            }

            Yii::log('Successfully calculated attendance data for student: ' . $student->_id, 'info', 'AttendanceHelper');
            
            return self::createSuccessResponse($attendanceData, 'Attendance data calculated successfully');

        } catch (Exception $e) {
            Yii::log('Error calculating attendance data: ' . $e->getMessage(), 'error', 'AttendanceHelper');
            return self::createErrorResponse('Failed to calculate attendance data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process attendance history and calculate statistics
     * 
     * @param array $attendanceHistory Raw attendance data
     * @param array $attendanceData Current attendance data structure
     * @return array Updated attendance data
     */
    private static function processAttendanceHistory($attendanceHistory, $attendanceData)
    {
        $subjectStats = [];

        // Validate that attendanceHistory is actually an array/object before foreach
        if (!is_array($attendanceHistory) && !is_object($attendanceHistory)) {
            throw new Exception('Invalid attendance history data format');
        }

        foreach ($attendanceHistory as $date => $records) {
            // Handle both array and single record formats
            $recordsArray = is_array($records) && isset($records[0]) ? $records : [$records];

            foreach ($recordsArray as $record) {
                if (!isset($record['status'])) continue;

                $attendanceData['statistics']['totalClasses']++;
                
                // Update statistics based on status
                switch ($record['status']) {
                    case 'present':
                        $attendanceData['statistics']['totalPresent']++;
                        break;
                    case 'absent':
                        $attendanceData['statistics']['totalAbsent']++;
                        break;
                    case 'late':
                        $attendanceData['statistics']['totalLate']++;
                        break;
                }

                // Track by subject
                $className = $record['class_name'] ?? 'Unknown';
                if (!isset($subjectStats[$className])) {
                    $subjectStats[$className] = ['total' => 0, 'present' => 0];
                }
                $subjectStats[$className]['total']++;
                if ($record['status'] === 'present') {
                    $subjectStats[$className]['present']++;
                }

                // Add to history
                $attendanceData['history'][] = [
                    'date' => $date,
                    'status' => $record['status'],
                    'class_name' => $className,
                    'notes' => $record['notes'] ?? ''
                ];
            }
        }

        // Calculate percentages by subject
        foreach ($subjectStats as $className => $stats) {
            $attendanceData['bySubject'][$className] = round(($stats['present'] / $stats['total']) * 100, 1);
        }

        // Sort history by date (most recent first)
        usort($attendanceData['history'], function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $attendanceData;
    }

    /**
     * Generate calendar data for daywise attendance view
     * 
     * @param Student $student The student model
     * @param int $currentYear Year for calendar
     * @param int $currentMonth Month for calendar
     * @param int $daysInMonth Number of days in month
     * @return array Standardized response with calendar data
     */
    public static function generateCalendarData($student, $currentYear, $currentMonth, $daysInMonth)
    {
        try {
            $calendarData = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = "$currentYear-$currentMonth-" . sprintf("%02d", $day);
                $dayTimestamp = strtotime($currentDate);
                $weekday = date('N', $dayTimestamp);

                $dayData = self::processDayAttendance($student, $currentDate, $dayTimestamp, $weekday);
                $calendarData[$currentDate] = $dayData;
            }

            return self::createSuccessResponse($calendarData, 'Calendar data generated successfully');

        } catch (Exception $e) {
            Yii::log('Error generating calendar data: ' . $e->getMessage(), 'error', 'AttendanceHelper');
            return self::createErrorResponse('Failed to generate calendar data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process attendance data for a specific day
     * 
     * @param Student $student The student model
     * @param string $currentDate Date in Y-m-d format
     * @param int $dayTimestamp Unix timestamp for the day
     * @param int $weekday Day of week (1-7)
     * @return array Day attendance data
     */
    private static function processDayAttendance($student, $currentDate, $dayTimestamp, $weekday)
    {
        $dayData = [
            'date' => $currentDate,
            'day' => (int)date('d', $dayTimestamp),
            'weekday' => $weekday,
            'status' => 'no-data',
            'details' => null
        ];

        // Skip future dates
        if ($dayTimestamp > time()) {
            $dayData['status'] = 'future';
            return $dayData;
        }

        // Skip weekends (6=Saturday, 7=Sunday)
        if ($weekday >= 6) {
            $dayData['status'] = 'weekend';
            $dayData['details'] = [
                'info' => 'Weekend - No Classes',
                'classes' => []
            ];
            return $dayData;
        }

        // Validate attendance data before accessing it as array
        if (!is_array($student->attendance) && !is_object($student->attendance)) {
            throw new Exception('Invalid student attendance data format');
        }

        // Check if we have attendance data for this date
        if (isset($student->attendance[$currentDate])) {
            $attendanceRecords = $student->attendance[$currentDate];
            $classes = [];
            $overallStatus = 'present';

            // Handle both array and single record formats
            if (is_array($attendanceRecords) && isset($attendanceRecords[0])) {
                $presentCount = 0;
                $totalCount = count($attendanceRecords);

                foreach ($attendanceRecords as $record) {
                    $classes[] = [
                        'name' => $record['class_name'] ?? 'Unknown Class',
                        'time' => 'Class Time',
                        'status' => $record['status'],
                        'instructor' => 'Instructor',
                        'notes' => $record['notes'] ?? ''
                    ];

                    if ($record['status'] === 'present') {
                        $presentCount++;
                    }
                }

                // Determine overall status
                if ($presentCount === 0) {
                    $overallStatus = 'absent';
                } elseif ($presentCount < $totalCount) {
                    $overallStatus = 'partial';
                }

            } elseif (isset($attendanceRecords['status'])) {
                $overallStatus = $attendanceRecords['status'];
                $classes[] = [
                    'name' => $attendanceRecords['class_name'] ?? 'Unknown Class',
                    'time' => 'Class Time',
                    'status' => $attendanceRecords['status'],
                    'instructor' => 'Instructor',
                    'notes' => $attendanceRecords['notes'] ?? ''
                ];
            }

            $dayData['status'] = $overallStatus;
            $dayData['details'] = [
                'check_in' => null,
                'check_out' => null,
                'classes' => $classes,
                'notes' => []
            ];
        }

        return $dayData;
    }

    /**
     * Save attendance data for a class
     * 
     * @param string $classId Class ID
     * @param string $attendanceDate Date in Y-m-d format
     * @param array $attendanceData Array of student attendance records
     * @return array Standardized response
     */
    public static function saveClassAttendance($classId, $attendanceDate, $attendanceData)
    {
        try {
            // Validate input parameters
            $validation = self::validateAttendanceInput($classId, $attendanceDate, $attendanceData);
            if (!$validation['success']) {
                return $validation;
            }

            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new MongoDB\BSON\ObjectId($classId));
            if (!$classroom) {
                return self::createErrorResponse('Classroom not found', 404);
            }

            // Initialize attendance array if not exists
            if (!isset($classroom->attendance)) {
                $classroom->attendance = array();
            }

            // Process attendance records
            $result = self::processClassAttendanceRecords($classroom, $attendanceDate, $attendanceData);
            if (!$result['success']) {
                return $result;
            }

            // Save classroom attendance
            if (!$classroom->save()) {
                throw new Exception('Failed to save classroom attendance');
            }

            Yii::log("Attendance saved successfully for class: $classId on date: $attendanceDate", 'info', 'AttendanceHelper');
            
            return self::createSuccessResponse(null, 'Attendance saved successfully');

        } catch (Exception $e) {
            Yii::log('Error saving attendance: ' . $e->getMessage(), 'error', 'AttendanceHelper');
            return self::createErrorResponse('Failed to save attendance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate attendance input data
     * 
     * @param string $classId Class ID
     * @param string $attendanceDate Date string
     * @param array $attendanceData Attendance records
     * @return array Validation result
     */
    private static function validateAttendanceInput($classId, $attendanceDate, $attendanceData)
    {
        $errors = [];

        if (empty($classId)) {
            $errors[] = 'Class ID is required';
        }

        if (empty($attendanceDate) || !strtotime($attendanceDate)) {
            $errors[] = 'Valid attendance date is required';
        }

        if (empty($attendanceData) || !is_array($attendanceData)) {
            $errors[] = 'Attendance data is required and must be an array';
            // Return early if not array to prevent foreach error
            if (!empty($errors)) {
                return self::createErrorResponse('Validation failed', 400, $errors);
            }
        }

        foreach ($attendanceData as $record) {
            if (!isset($record['student_id']) || empty($record['student_id'])) {
                $errors[] = 'Student ID is required for all records';
            }
            if (!isset($record['status']) || !in_array($record['status'], ['present', 'absent', 'late'])) {
                $errors[] = 'Valid status (present, absent, late) is required for all records';
            }
        }

        if (!empty($errors)) {
            return self::createErrorResponse('Validation failed', 400, $errors);
        }

        return self::createSuccessResponse(null, 'Validation passed');
    }

    /**
     * Process and save attendance records for a class
     * 
     * @param ClassRoom $classroom Classroom model
     * @param string $attendanceDate Date string
     * @param array $attendanceData Attendance records
     * @return array Processing result
     */
    private static function processClassAttendanceRecords($classroom, $attendanceDate, $attendanceData)
    {
        try {
            // Validate classroom->students before using count()
            if (!is_array($classroom->students) && !is_countable($classroom->students)) {
                throw new Exception('Invalid classroom students data format');
            }

            // Create attendance record for this date
            $classroom->attendance[$attendanceDate] = array(
                'date' => $attendanceDate,
                'total_students' => count($classroom->students),
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'student_records' => array()
            );

            // Process each student's attendance
            foreach ($attendanceData as $record) {
                $studentId = $record['student_id'];
                $status = $record['status'];

                // Update classroom attendance counts
                $classroom->attendance[$attendanceDate][$status]++;

                // Store individual student record
                $classroom->attendance[$attendanceDate]['student_records'][$studentId] = array(
                    'status' => $status,
                    'notes' => isset($record['notes']) ? $record['notes'] : ''
                );

                // Update student's individual attendance record
                $studentResult = self::updateStudentAttendance($studentId, $attendanceDate, $status, $classroom, $record);
                if (!$studentResult['success']) {
                    Yii::log('Warning: Failed to update student attendance for student: ' . $studentId, 'warning', 'AttendanceHelper');
                }
            }

            return self::createSuccessResponse(null, 'Attendance records processed successfully');

        } catch (Exception $e) {
            return self::createErrorResponse('Failed to process attendance records: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update individual student attendance record
     * 
     * @param string $studentId Student ID
     * @param string $attendanceDate Date string
     * @param string $status Attendance status
     * @param ClassRoom $classroom Classroom model
     * @param array $record Full attendance record
     * @return array Update result
     */
    private static function updateStudentAttendance($studentId, $attendanceDate, $status, $classroom, $record)
    {
        try {
            $student = Student::model()->findByPk(new MongoDB\BSON\ObjectId($studentId));
            if (!$student) {
                return self::createErrorResponse('Student not found: ' . $studentId, 404);
            }

            if (!isset($student->attendance)) {
                $student->attendance = array();
            }

            if (!isset($student->attendance[$attendanceDate])) {
                $student->attendance[$attendanceDate] = array();
            }

            // Check if class already exists for that date and update or add
            $updated = false;
            if (is_array($student->attendance[$attendanceDate])) {
                foreach ($student->attendance[$attendanceDate] as &$existingRecord) {
                    if (isset($existingRecord['class_id']) && $existingRecord['class_id'] === (string)$classroom->_id) {
                        $existingRecord['status'] = $status;
                        $existingRecord['notes'] = isset($record['notes']) ? $record['notes'] : '';
                        $updated = true;
                        break;
                    }
                }
            }

            if (!$updated) {
                $student->attendance[$attendanceDate][] = array(
                    'class_id' => (string)$classroom->_id,
                    'class_name' => $classroom->class_name,
                    'status' => $status,
                    'notes' => isset($record['notes']) ? $record['notes'] : ''
                );
            }

            // Recalculate percentage
            $student->percentage = self::calculateStudentPercentage($student->attendance);

            if (!$student->save()) {
                throw new Exception('Failed to save student attendance');
            }

            return self::createSuccessResponse(null, 'Student attendance updated successfully');

        } catch (Exception $e) {
            return self::createErrorResponse('Failed to update student attendance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Calculate student's overall attendance percentage
     * 
     * @param array $attendanceHistory Student's attendance history
     * @return float Attendance percentage
     */
    private static function calculateStudentPercentage($attendanceHistory)
    {
        $totalRecords = 0;
        $presentCount = 0;

        foreach ($attendanceHistory as $dateEntries) {
            if (is_array($dateEntries)) {
                foreach ($dateEntries as $entry) {
                    if (is_array($entry) && isset($entry['status'])) {
                        $totalRecords++;
                        if ($entry['status'] === 'present') {
                            $presentCount++;
                        }
                    }
                }
            }
        }

        return ($totalRecords > 0) ? round(($presentCount / $totalRecords) * 100, 1) : 100.0;
    }

    /**
     * Create standardized success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @return array Standardized response
     */
    private static function createSuccessResponse($data = null, $message = 'Operation successful')
    {
        return array_merge(self::$responseTemplate, [
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
    }

    /**
     * Create standardized error response
     * 
     * @param string $message Error message
     * @param int $code Error code
     * @param array $errors Additional error details
     * @return array Standardized response
     */
    private static function createErrorResponse($message = 'Operation failed', $code = 500, $errors = [])
    {
        return array_merge(self::$responseTemplate, [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors
        ]);
    }
}