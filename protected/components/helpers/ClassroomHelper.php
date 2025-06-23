<?php

/**
 * ClassroomHelper - Handles all classroom-related business logic
 * 
 * This class encapsulates classroom operations including:
 * - Classroom data retrieval and processing
 * - Student management within classrooms
 * - Performance analytics and statistics
 * - Data validation and error handling
 */
class ClassroomHelper extends CComponent
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
     * Get detailed classroom information with students and analytics
     * 
     * @param string $classroomId Classroom ID
     * @return array Standardized response with classroom details
     */
    public static function getClassroomDetails($classroomId)
    {
        try {
            // Validate input
            if (empty($classroomId)) {
                return self::createErrorResponse('Classroom ID is required', 400);
            }

            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new MongoDB\BSON\ObjectId($classroomId));
            if (!$classroom) {
                return self::createErrorResponse('Classroom not found', 404);
            }

            // Build classroom details
            $classDetails = self::buildClassroomDetails($classroom);
            
            // Get students data
            $studentsResult = self::getClassroomStudents($classroom);
            if (!$studentsResult['success']) {
                return $studentsResult;
            }

            // Generate performance analytics
            $performanceResult = self::generatePerformanceAnalytics($classroom);
            if (!$performanceResult['success']) {
                return $performanceResult;
            }

            $response = [
                'classDetails' => $classDetails,
                'students' => $studentsResult['data'],
                'performanceData' => $performanceResult['data']
            ];

            Yii::log('Successfully retrieved classroom details for: ' . $classroomId, 'info', 'ClassroomHelper');
            
            return self::createSuccessResponse($response, 'Classroom details retrieved successfully');

        } catch (Exception $e) {
            Yii::log('Error retrieving classroom details: ' . $e->getMessage(), 'error', 'ClassroomHelper');
            return self::createErrorResponse('Failed to retrieve classroom details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Build basic classroom details structure
     * 
     * @param ClassRoom $classroom Classroom model
     * @return array Classroom details
     */
    private static function buildClassroomDetails($classroom)
    {
        $classesConducted = 0;
        $lastAttendanceDate = '';

        // Process attendance data if available
        if (isset($classroom->attendance) && is_array($classroom->attendance) && !empty($classroom->attendance)) {
            $classesConducted = count($classroom->attendance);
            $attendanceDates = array_keys($classroom->attendance);
            sort($attendanceDates);
            $lastAttendanceDate = end($attendanceDates);
        } else {
            $lastAttendanceDate = date('Y-m-d');
        }

        return [
            '_id' => (string) $classroom->_id,
            'class_name' => $classroom->class_name,
            'subject' => $classroom->subject,
            'academic_year' => $classroom->academic_year,
            'grade_level' => isset($classroom->grade_level) ? $classroom->grade_level : '',
            'section' => isset($classroom->section) ? $classroom->section : '',
            'schedule' => isset($classroom->schedule) ? $classroom->schedule : [
                'days' => 'Monday, Wednesday, Friday',
                'time' => '09:00 AM - 10:30 AM',
                'room' => isset($classroom->room) ? $classroom->room : 'Main Building'
            ],
            'total_students' => isset($classroom->students) ? count($classroom->students) : 0,
            'last_attendance_date' => $lastAttendanceDate,
            'total_classes' => isset($classroom->total_classes) ? $classroom->total_classes : $classesConducted + 10,
            'classes_conducted' => $classesConducted,
            'attendance_rate' => 0 // Will be calculated later
        ];
    }

    /**
     * Get and process students data for a classroom
     * 
     * @param ClassRoom $classroom Classroom model
     * @return array Standardized response with students data
     */
    private static function getClassroomStudents($classroom)
    {
        try {
            $studentsList = [];
            $totalPresent = 0;
            $totalAbsent = 0;

            if (!isset($classroom->students) || !is_array($classroom->students)) {
                return self::createSuccessResponse($studentsList, 'No students found in classroom');
            }

            foreach ($classroom->students as $studentId => $studentInfo) {
                $student = Student::model()->findByPk(new MongoDB\BSON\ObjectId($studentId));
                
                if ($student) {
                    $studentData = self::processStudentAttendanceData($student);
                    $studentsList[] = $studentData;
                    
                    $totalPresent += $studentData['total_present'];
                    $totalAbsent += $studentData['total_absent'];
                }
            }

            // Calculate overall attendance rate for the class
            $totalAttendances = $totalPresent + $totalAbsent;
            $attendanceRate = ($totalAttendances > 0) ? round(($totalPresent / $totalAttendances) * 100) : 0;

            return self::createSuccessResponse([
                'students' => $studentsList,
                'attendance_rate' => $attendanceRate,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent
            ], 'Students data processed successfully');

        } catch (Exception $e) {
            return self::createErrorResponse('Failed to process students data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process individual student attendance data
     * 
     * @param Student $student Student model
     * @return array Student attendance data
     */
    private static function processStudentAttendanceData($student)
    {
        $attendancePercentage = isset($student->percentage) ? $student->percentage : 100;
        $totalAttendances = 0;
        $presentCount = 0;
        $lastStatus = 'present';
        $lastAttendanceDay = '';
        $consecutiveAbsences = 0;

        // Calculate attendance counts from actual data
        if (isset($student->attendance) && is_array($student->attendance)) {
            $totalAttendances = count($student->attendance);
            $attendanceDays = array_keys($student->attendance);
            sort($attendanceDays);

            if (!empty($attendanceDays)) {
                $lastAttendanceDay = end($attendanceDays);
                $lastRecord = $student->attendance[$lastAttendanceDay];
                
                // Handle both array and single record formats
                if (is_array($lastRecord) && isset($lastRecord[0])) {
                    $lastStatus = $lastRecord[0]['status'];
                } elseif (isset($lastRecord['status'])) {
                    $lastStatus = $lastRecord['status'];
                }

                // Count present days and calculate consecutive absences
                $absenceCount = 0;
                foreach ($student->attendance as $date => $record) {
                    $status = is_array($record) && isset($record[0]) ? $record[0]['status'] : $record['status'];
                    if ($status == 'present') {
                        $presentCount++;
                    }
                }

                // Calculate consecutive absences from the end
                for ($i = count($attendanceDays) - 1; $i >= 0; $i--) {
                    $currentDay = $attendanceDays[$i];
                    $record = $student->attendance[$currentDay];
                    $status = is_array($record) && isset($record[0]) ? $record[0]['status'] : $record['status'];
                    
                    if ($status == 'absent') {
                        $absenceCount++;
                    } else {
                        break;
                    }
                }
                $consecutiveAbsences = $absenceCount;
            }
        }

        // Use calculated percentage if available
        if ($totalAttendances > 0) {
            $studentAttendancePercentage = round(($presentCount / $totalAttendances) * 100);
        } else {
            $studentAttendancePercentage = $attendancePercentage;
        }

        $absentCount = $totalAttendances - $presentCount;

        return [
            '_id' => (string) $student->_id,
            'student_id' => $student->roll_no,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'attendance_percentage' => $studentAttendancePercentage,
            'total_present' => $presentCount,
            'total_absent' => $absentCount,
            'last_status' => $lastStatus,
            'last_attendance_date' => $lastAttendanceDay ? $lastAttendanceDay : date('Y-m-d'),
            'consecutive_absences' => $consecutiveAbsences
        ];
    }

    /**
     * Generate performance analytics for a classroom
     * 
     * @param ClassRoom $classroom Classroom model
     * @return array Standardized response with performance data
     */
    private static function generatePerformanceAnalytics($classroom)
    {
        try {
            // Initialize analytics structure
            $monthlyAttendance = array_fill_keys([
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ], 0);

            $weeklyAttendance = array_fill_keys(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'], 0);
            $dayAttendance = array_fill_keys(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 0);

            $presentCount = 0;
            $absentCount = 0;

            // Process classroom attendance records
            if (isset($classroom->attendance) && is_array($classroom->attendance)) {
                foreach ($classroom->attendance as $date => $record) {
                    self::processAttendanceRecord($date, $record, $monthlyAttendance, $weeklyAttendance, $dayAttendance);
                    
                    if (isset($record['present'])) {
                        $presentCount += $record['present'];
                    }
                    if (isset($record['absent'])) {
                        $absentCount += $record['absent'];
                    }
                }
            }

            // Apply default values if no data
            $defaultRate = 90;
            $monthlyAttendance = self::applyDefaultRates($monthlyAttendance, $defaultRate);
            $weeklyAttendance = self::applyDefaultRates($weeklyAttendance, $defaultRate);
            $dayAttendance = self::applyDefaultRates($dayAttendance, $defaultRate);

            // Calculate status distribution
            $totalStatuses = $presentCount + $absentCount;
            $statusDistribution = [
                'Present' => $totalStatuses > 0 ? round(($presentCount / $totalStatuses) * 100) : 85,
                'Absent' => $totalStatuses > 0 ? round(($absentCount / $totalStatuses) * 100) : 15,
            ];

            // Ensure distribution adds up to 100%
            if ($statusDistribution['Present'] + $statusDistribution['Absent'] != 100) {
                $statusDistribution['Present'] = 100 - $statusDistribution['Absent'];
            }

            $performanceData = [
                'monthly_attendance' => $monthlyAttendance,
                'weekly_attendance' => $weeklyAttendance,
                'attendance_by_day' => $dayAttendance,
                'status_distribution' => $statusDistribution
            ];

            return self::createSuccessResponse($performanceData, 'Performance analytics generated successfully');

        } catch (Exception $e) {
            return self::createErrorResponse('Failed to generate performance analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process individual attendance record for analytics
     * 
     * @param string $date Date string
     * @param array $record Attendance record
     * @param array &$monthlyAttendance Monthly attendance data (by reference)
     * @param array &$weeklyAttendance Weekly attendance data (by reference)
     * @param array &$dayAttendance Daily attendance data (by reference)
     */
    private static function processAttendanceRecord($date, $record, &$monthlyAttendance, &$weeklyAttendance, &$dayAttendance)
    {
        try {
            $dateObj = new DateTime($date);
            $month = $dateObj->format('F');
            $weekNumber = ceil($dateObj->format('j') / 7);
            $weekKey = 'Week ' . $weekNumber;
            $dayOfWeek = $dateObj->format('l');

            $totalStudentsOnDay = isset($record['total_students']) ? $record['total_students'] : 0;
            $presentOnDay = isset($record['present']) ? $record['present'] : 0;

            if ($totalStudentsOnDay > 0) {
                $rate = round(($presentOnDay / $totalStudentsOnDay) * 100);

                // Update monthly attendance
                if (isset($monthlyAttendance[$month])) {
                    $monthlyAttendance[$month] = $monthlyAttendance[$month] == 0 ? $rate : ($monthlyAttendance[$month] + $rate) / 2;
                }

                // Update weekly attendance
                if (isset($weeklyAttendance[$weekKey])) {
                    $weeklyAttendance[$weekKey] = $weeklyAttendance[$weekKey] == 0 ? $rate : ($weeklyAttendance[$weekKey] + $rate) / 2;
                }

                // Update daily attendance
                if (isset($dayAttendance[$dayOfWeek])) {
                    $dayAttendance[$dayOfWeek] = $dayAttendance[$dayOfWeek] == 0 ? $rate : ($dayAttendance[$dayOfWeek] + $rate) / 2;
                }
            }
        } catch (Exception $e) {
            Yii::log('Error processing attendance record for date: ' . $date . ' - ' . $e->getMessage(), 'warning', 'ClassroomHelper');
        }
    }

    /**
     * Apply default rates to empty analytics data
     * 
     * @param array $data Analytics data array
     * @param int $defaultRate Default rate to apply
     * @return array Updated data with default rates
     */
    private static function applyDefaultRates($data, $defaultRate)
    {
        if (array_sum($data) == 0) {
            return array_fill_keys(array_keys($data), $defaultRate);
        }
        return $data;
    }

    /**
     * Get classes assigned to a teacher
     * 
     * @param string $teacherId Teacher ID
     * @return array Standardized response with teacher's classes
     */
    public static function getTeacherClasses($teacherId)
    {
        try {
            if (empty($teacherId)) {
                return self::createErrorResponse('Teacher ID is required', 400);
            }

            $teacher = Teacher::model()->findByPk(new MongoDB\BSON\ObjectId($teacherId));
            if (!$teacher) {
                return self::createErrorResponse('Teacher not found', 404);
            }

            $teacherData = $teacher->getAttributes();
            $classDetails = [];

            if (isset($teacherData['classes']) && is_array($teacherData['classes']) && !empty($teacherData['classes'])) {
                foreach ($teacherData['classes'] as $classId) {
                    $classroom = ClassRoom::model()->findByPk(new MongoDB\BSON\ObjectId($classId));
                    if ($classroom !== null) {
                        $classDetails[] = [
                            '_id' => $classroom->_id,
                            'class_name' => $classroom->class_name,
                            'subject' => $classroom->subject,
                            'academic_year' => $classroom->academic_year
                        ];
                    }
                }
            }

            $teacherData['classes'] = $classDetails;

            return self::createSuccessResponse($teacherData, 'Teacher classes retrieved successfully');

        } catch (Exception $e) {
            Yii::log('Error retrieving teacher classes: ' . $e->getMessage(), 'error', 'ClassroomHelper');
            return self::createErrorResponse('Failed to retrieve teacher classes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get students for attendance taking
     * 
     * @param string $classId Class ID
     * @return array Standardized response with students for attendance
     */
    public static function getStudentsForAttendance($classId)
    {
        try {
            if (empty($classId)) {
                return self::createErrorResponse('Class ID is required', 400);
            }

            $classroom = ClassRoom::model()->findByPk(new MongoDB\BSON\ObjectId($classId));
            if (!$classroom) {
                return self::createErrorResponse('Classroom not found', 404);
            }

            $students = [];
            if (isset($classroom->students) && is_array($classroom->students)) {
                foreach ($classroom->students as $studentId => $studentInfo) {
                    $student = Student::model()->findByPk(new MongoDB\BSON\ObjectId($studentId));
                    if ($student) {
                        $students[] = [
                            'id' => (string)$student->_id,
                            'name' => $student->first_name . ' ' . $student->last_name,
                            'roll_no' => $student->roll_no,
                            'email' => $student->email
                        ];
                    }
                }
            }

            return self::createSuccessResponse($students, 'Students retrieved for attendance');

        } catch (Exception $e) {
            Yii::log('Error retrieving students for attendance: ' . $e->getMessage(), 'error', 'ClassroomHelper');
            return self::createErrorResponse('Failed to retrieve students: ' . $e->getMessage(), 500);
        }
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