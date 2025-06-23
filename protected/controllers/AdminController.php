<?php

use MongoDB\BSON\ObjectId;
class AdminController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                    'manageStudents',
                    'createStudent',
                    'updateStudent',
                    'getClassrooms',
                    'assignClassToStudent',
                    'removeClassFromStudent',
                    'deleteStudent',
                    'getStudent',
                    'manageTeachers',
                    'getTeacher',
                    'createTeacher',
                    'updateTeacher',
                    'deleteTeacher',
                    'assignClassToTeacher',
                    'removeClassFromTeacher',
                    'manageClasses',
                    'createClassroom',
                    'updateClassroom',
                    'deleteClassroom',
                    'getClassroom',
                    'getStudents',
                    'addStudentToClass',
                    'removeStudentFromClass',
                    'getAllTeachers',
                    'GetFreeTeachers',
                    'attendance',
                    'saveAttendance',
                ),
                'users' => array('admin'), // In production this should be restricted to admin users
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Manages all students.
     */
    public function actionManageStudents()
    {
        // Get all students from the database
        $students = Student::model()->findAll();

        // For statistics
        $totalStudents = count($students);
        $activeStudents = 0;
        $inactiveStudents = 0;

        // Initialize attendance statistics with zeros
        $attendanceStats = array(
            'excellent' => 0, // >90%
            'good' => 0,      // 80-90%
            'average' => 0,   // 70-80%
            'poor' => 0       // <70%
        );

        // Process each student for statistics
        if ($totalStudents > 0) {
            foreach ($students as $student) {
                // For this example, we'll consider students with classes as active
                if (!empty($student->classes)) {
                    $activeStudents++;
                } else {
                    $inactiveStudents++;
                }

                // Calculate attendance statistics
                if ($student->percentage >= 90) {
                    $attendanceStats['excellent']++;
                } elseif ($student->percentage >= 80) {
                    $attendanceStats['good']++;
                } elseif ($student->percentage >= 70) {
                    $attendanceStats['average']++;
                } else {
                    $attendanceStats['poor']++;
                }
            }
        }

        // Get attendance trend data
        $attendanceTrendData = $this->getAttendanceTrendData($students);

        // Render the view with data
        $this->render('managestudents', array(
            'students' => $students,
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'inactiveStudents' => $inactiveStudents,
            'attendanceStats' => $attendanceStats,
            'attendanceTrendData' => $attendanceTrendData
        ));
    }

    /**
     * Get student data for editing or viewing
     * This is an API endpoint that would be called via AJAX
     */
    public function actionGetStudent($id)
    {
        // Find the student by ID
        try {
            $studentModel = $this->getStudentModel();
            $student = $studentModel->findByPk(new ObjectId($id));

            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            // Return student data
            echo CJSON::encode(array(
                'success' => true,
                'student' => $student->getAttributes(null, array('password'))
            ));

        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error retrieving student: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Creates a new student
     * This is an API endpoint that would be called via AJAX
     */
    public function actionCreateStudent()
    {
        // This would process the form data and create a new student
        if (isset($_POST['Student'])) {
            $student = $this->createStudentInstance();

            // Handle password hashing
            if (!empty($_POST['Student']['password'])) {
                $_POST['Student']['password'] = $student->hashPassword($_POST['Student']['password']);
            }

            $student->attributes = $_POST['Student'];

            // Create an address if submitted
            if (isset($_POST['Address'])) {
                $address = $this->createAddressInstance();
                $address->attributes = $_POST['Address'];
                $student->address = $address;
            }

            if ($student->save()) {
                echo CJSON::encode(array('success' => true));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to save student',
                    'errors' => $student->errors
                ));
            }
        } else {
            echo CJSON::encode(array('success' => false, 'message' => 'No data submitted'));
        }
    }

    /**
     * Updates an existing student
     * This is an API endpoint that would be called via AJAX
     */
    public function actionUpdateStudent($id)
    {
        try {
            // Find the student by ID
            $studentModel = $this->getStudentModel();
            $student = $studentModel->findByPk(new ObjectId($id));

            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            if (isset($_POST['Student'])) {
                // Don't update password if it's empty
                if (empty($_POST['Student']['password'])) {
                    unset($_POST['Student']['password']);
                } else {
                    $_POST['Student']['password'] = $student->hashPassword($_POST['Student']['password']);
                }

                $student->attributes = $_POST['Student'];

                // Update the address if submitted
                if (isset($_POST['Address'])) {
                    if ($student->address === null) {
                        $student->address = $this->createAddressInstance();
                    }
                    $student->address->attributes = $_POST['Address'];
                }

                if ($student->save()) {
                    echo CJSON::encode(array('success' => true));
                } else {
                    echo CJSON::encode(array(
                        'success' => false,
                        'message' => 'Failed to update student',
                        'errors' => $student->errors
                    ));
                }
            } else {
                echo CJSON::encode(array('success' => false, 'message' => 'No data submitted'));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error updating student: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Deletes a student
     * This is an API endpoint that would be called via AJAX
     */
    public function actionDeleteStudent($id)
    {
        try {
            // Find the student by ID
            $studentModel = $this->getStudentModel();
            $student = $studentModel->findByPk(new ObjectId($id));

            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            if ($student->delete()) {
                echo CJSON::encode(array('success' => true));
            } else {
                echo CJSON::encode(array('success' => false, 'message' => 'Failed to delete student'));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error deleting student: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Gets available classrooms for dropdown selection
     * This is an API endpoint that would be called via AJAX
     */
    public function actionGetClassrooms()
    {
        try {
            // Get all classrooms from the database
            $classroomModel = $this->getClassRoomModel();
            $classrooms = $classroomModel->findAll();

            $result = array();
            foreach ($classrooms as $classroom) {
                $result[] = array(
                    'id' => (string) $classroom->_id,
                    'name' => $classroom->class_name,
                    'subject' => $classroom->subject,
                    'academicYear' => $classroom->academic_year
                );
            }

            echo CJSON::encode(array('success' => true, 'classrooms' => $result));
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error retrieving classrooms: ' . $e->getMessage()
            ));
        }
    }

    // Helper methods for dependency injection (can be overridden in tests)
    protected function getStudentModel()
    {
        return Student::model();
    }

    protected function getTeacherModel()
    {
        return Teacher::model();
    }

    protected function getClassRoomModel()
    {
        return ClassRoom::model();
    }

    protected function createStudentInstance()
    {
        return new Student();
    }

    protected function createTeacherInstance()
    {
        return new Teacher();
    }

    protected function createClassRoomInstance()
    {
        return new ClassRoom();
    }

    protected function createAddressInstance()
    {
        return new Address();
    }

    /**
     * Assigns a classroom to a student
     * This is an API endpoint that would be called via AJAX
     */
    public function actionAssignClassToStudent()
    {
        if (!isset($_POST['studentId']) || !isset($_POST['classroomId'])) {
            echo CJSON::encode(array('success' => false, 'message' => 'Missing required parameters'));
            return;
        }

        $studentId = $_POST['studentId'];
        $classroomId = $_POST['classroomId'];

        try {
            // Find the student
            $student = Student::model()->findByPk(new ObjectId($studentId));
            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new ObjectId($classroomId));
            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Check if the class is already assigned
            if (isset($student->classes[$classroom->class_name])) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student is already assigned to this class'));
                return;
            }

            // Assign the class to the student
            if (!is_array($student->classes)) {
                $student->classes = array();
            }

            // Updated format to consistently include the classroom _id
            $classObjId = (string) $classroom->_id;
            $student->classes[$classroom->class_name] = array(
                '_id' => $classObjId, // Added this line to include _id explicitly
                'id' => $classObjId,  // Keep this for backward compatibility
                'subject' => $classroom->subject,
                'academic_year' => $classroom->academic_year
            );

            // Update the classroom's students list
            if (!isset($classroom->students) || !is_array($classroom->students)) {
                $classroom->students = array();
            }

            $classroom->students[(string) $student->_id] = array(
                'name' => $student->first_name . ' ' . $student->last_name,
                'roll_no' => $student->roll_no
            );

            // Save both documents
            if ($student->save() && $classroom->save()) {
                echo CJSON::encode(array(
                    'success' => true,
                    'classroom' => array(
                        'id' => (string) $classroom->_id,
                        'name' => $classroom->class_name,
                        'subject' => $classroom->subject,
                        'academic_year' => $classroom->academic_year
                    )
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to save changes',
                    'studentErrors' => $student->errors,
                    'classroomErrors' => $classroom->errors
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error assigning class: ' . $e->getMessage()
            ));
        }
    }


    // CLASSROOM TO STUDENT MAP

    public function actionRemoveClassFromStudent()
    {
        if (!isset($_POST['studentId']) || !isset($_POST['className'])) {
            echo CJSON::encode(array('success' => false, 'message' => 'Missing required parameters'));
            return;
        }

        $studentId = $_POST['studentId'];
        $className = $_POST['className'];

        try {
            // Find the student
            $student = Student::model()->findByPk(new ObjectId($studentId));
            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            // Check if the class exists in student's classes
            if (!isset($student->classes[$className])) {
                echo CJSON::encode(array('success' => false, 'message' => 'Class not found in student\'s classes'));
                return;
            }

            // Get the classroom ID to update the classroom document
            $classroomId = $student->classes[$className]['id'];

            // Remove the class from the student
            unset($student->classes[$className]);

            // Find the classroom and remove the student
            if (!empty($classroomId)) {
                $classroom = ClassRoom::model()->findByPk(new ObjectId($classroomId));
                if ($classroom !== null) {
                    unset($classroom->students[(string) $student->_id]);
                    $classroom->save();
                }
            }

            // Save the student
            if ($student->save()) {
                echo CJSON::encode(array('success' => true));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to save changes',
                    'errors' => $student->errors
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error removing class: ' . $e->getMessage()
            ));
        }
    }


    // TEACHER MANAGEMENT

    public function actionGetAllTeachers()
    {
        // echo "hi";
        // exit;
        $teachers = Teacher::model()->findAll(); // Fetch all teachers
        // print_r($teachers);
        foreach ($teachers as $teacher) {
            if (isset($teacher->classes) && is_array($teacher->classes)) {
                $classDetails = [];
                foreach ($teacher->classes as $class) {
                    $classroom = ClassRoom::model()->findByPk(new ObjectId($class));
                    if ($classroom !== null) {
                        $classDetails[] = array(
                            'id' => (string) $classroom->_id,
                            'name' => $classroom->class_name,
                            'subject' => $classroom->subject,
                            'academic_year' => $classroom->academic_year
                        );
                    }
                }
                $teacher->classes = $classDetails;
            }
        }

        $this->render('manageteachers', array(
            'teachers' => $teachers,
        ));
    }

    // Action to create a new teacher
    public function actionCreateTeacher()
    {
        try {
            $model = new Teacher();

            if (isset($_POST['Teacher'])) {
                // Handle password hashing
                if (!empty($_POST['Teacher']['password'])) {
                    $_POST['Teacher']['password'] = $model->hashPassword($_POST['Teacher']['password']);
                }

                $model->attributes = $_POST['Teacher'];

                // Create an address if submitted
                if (isset($_POST['Address'])) {
                    $address = new Address();
                    $address->attributes = $_POST['Address'];
                    $model->address = $address;
                }

                // Handle qualifications if any
                if (isset($_POST['Qualifications']) && is_array($_POST['Qualifications'])) {
                    $model->Qualifications = [];
                    foreach ($_POST['Qualifications'] as $qualData) {
                        $qualification = new Qualification();
                        $qualification->attributes = $qualData;
                        $model->Qualifications[] = $qualification;
                    }
                }

                if ($model->save()) {
                    echo CJSON::encode(['success' => true, 'message' => 'Teacher created successfully.']);
                    Yii::app()->end();
                } else {
                    echo CJSON::encode([
                        'success' => false,
                        'message' => 'Failed to save teacher',
                        'errors' => $model->getErrors()
                    ]);
                    Yii::app()->end();
                }
            } else {
                echo CJSON::encode(['success' => false, 'message' => 'No data submitted']);
                Yii::app()->end();
            }
        } catch (Exception $e) {
            echo CJSON::encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            Yii::app()->end();
        }
    }

    // Action to view the details of a specific teacher
    public function actionGetTeacher($id)
    {
        // Find the teacher by ID
        try {
            $teacher = Teacher::model()->findByPk(new ObjectId($id));

            if ($teacher === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Teacher not found'));
                return;
            }

            // Get teacher attributes, excluding password
            $teacherData = $teacher->getAttributes(null, array('password'));


            // Ensure classes is always returned as an array/object, not null
            if (!isset($teacherData['classes']) || $teacherData['classes'] === null) {
                $teacherData['classes'] = array();
            } else {
                // If classes are present, fetch their details
                $classDetails = [];
                foreach ($teacherData['classes'] as $class) {
                    $classroom = ClassRoom::model()->findByPk(new ObjectId($class));
                    if ($classroom !== null) {
                        $classDetails[] = array(
                            'id' => (string) $classroom->_id,
                            'name' => $classroom->class_name,
                            'subject' => $classroom->subject,
                            'academic_year' => $classroom->academic_year
                        );
                    }
                }
                $teacherData['classes'] = $classDetails;
            }

            // Return teacher data
            echo CJSON::encode(array(
                'success' => true,
                'teacher' => $teacherData
            ));

        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error retrieving teacher: ' . $e->getMessage()
            ));
        }
    }

    // Action to update a specific teacher's details
    public function actionUpdateTeacher($id)
    {
        $model = Teacher::model()->findByPk(new ObjectId($id));
        if ($model === null) {
            echo CJSON::encode(['success' => false, 'message' => 'Teacher not found.']);
            return;
        }
        if (isset($_POST['Teacher'])) {
            unset($_POST['Teacher']['password']); // Prevent password update
            $model->attributes = $_POST['Teacher'];

            if ($model->save()) {
                echo CJSON::encode(['success' => true, 'message' => 'Teacher updated successfully.']);
                return;
            }

            echo CJSON::encode(['success' => false, 'errors' => $model->getErrors()]);
        }
    }


    // Action to delete a specific teacher
    public function actionDeleteTeacher()
    {
        if (Yii::app()->request->isPostRequest) {
            $id = Yii::app()->request->getPost('id');
            if ($id) {
                $teacher = Teacher::model()->findByPk(new ObjectId($id));
                if ($teacher !== null && $teacher->delete()) {
                    echo CJSON::encode(['success' => true, 'message' => 'Teacher deleted successfully.']);
                    return;
                }
            }
            echo CJSON::encode(['success' => false, 'error' => 'Teacher not found or could not be deleted.']);
            return;
        }
        throw new CHttpException(400, 'Invalid request.');
    }

    /**
     * Manages all teachers
     */
    public function actionManageTeachers()
    {
        // Get all teachers from the database
        $teachers = Teacher::model()->findAll();

        foreach ($teachers as $teacher) {
            if (isset($teacher->classes) && is_array($teacher->classes)) {
                $classDetails = [];
                foreach ($teacher->classes as $class) {
                    $classroom = ClassRoom::model()->findByPk(new ObjectId($class));
                    if ($classroom !== null) {
                        $classDetails[] = array(
                            'id' => (string) $classroom->_id,
                            'name' => $classroom->class_name,
                            'subject' => $classroom->subject,
                            'academic_year' => $classroom->academic_year
                        );
                    }
                }
                $teacher->classes = $classDetails;
            }
        }

        // Render the view with data
        $this->render('manageteachers', array(
            'teachers' => $teachers
        ));
    }

    /**
     * Assigns a classroom to a teacher
     * This is an API endpoint that would be called via AJAX
     */
    public function actionAssignClassToTeacher()
    {
        if (!isset($_POST['teacherId']) || !isset($_POST['classroomId'])) {
            echo CJSON::encode(array('success' => false, 'message' => 'Missing required parameters'));
            return;
        }

        $teacherId = $_POST['teacherId'];
        $classroomId = $_POST['classroomId'];

        try {
            // Find the teacher
            $teacher = Teacher::model()->findByPk(new ObjectId($teacherId));
            if ($teacher === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Teacher not found'));
                return;
            }

            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new ObjectId($classroomId));
            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Check if the class is already assigned
            $classroomObjectId = (string) $classroom->_id;
            if (in_array($classroomObjectId, $teacher->classes)) {
                echo CJSON::encode(array('success' => false, 'message' => 'Teacher is already assigned to this class'));
                return;
            }

            // Assign the classroom to the teacher (store only the ObjectId)
            if (!is_array($teacher->classes)) {
                $teacher->classes = array();
            }

            $teacher->classes[] = $classroomObjectId;

            // Update the classroom's teacher_id if needed
            $classroom->class_teacher_id = (string) $teacher->_id;

            // Save both documents
            if ($teacher->save() && $classroom->save()) {
                echo CJSON::encode(array(
                    'success' => true,
                    'classroom' => array(
                        'id' => (string) $classroom->_id,
                        'name' => $classroom->class_name,
                        'subject' => $classroom->subject,
                        'academic_year' => $classroom->academic_year
                    )
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to save changes',
                    'teacherErrors' => $teacher->errors,
                    'classroomErrors' => $classroom->errors
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error assigning class: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Removes a classroom from a teacher
     * This is an API endpoint that would be called via AJAX
     */
    public function actionRemoveClassFromTeacher()
    {
        if (!isset($_POST['teacherId']) || !isset($_POST['classroomId'])) {
            echo CJSON::encode(array('success' => false, 'message' => 'Missing required parameters'));
            return;
        }

        $teacherId = $_POST['teacherId'];
        $classroomId = $_POST['classroomId'];

        try {
            // Find the teacher
            $teacher = Teacher::model()->findByPk(new ObjectId($teacherId));
            if ($teacher === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Teacher not found'));
                return;
            }

            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new ObjectId($classroomId));
            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Check if the class exists in teacher's classes
            $classroomObjectId = (string) $classroom->_id;
            $classIndex = array_search($classroomObjectId, $teacher->classes);

            if ($classIndex === false) {
                echo CJSON::encode(array('success' => false, 'message' => 'Class not found in teacher\'s classes'));
                return;
            }

            // Remove the class from the teacher
            unset($teacher->classes[$classIndex]);
            $teacher->classes = array_values($teacher->classes); // Re-index array

            // Find the classroom and update teacher_id
            if ($classroom !== null && $classroom->class_teacher_id == (string) $teacher->_id) {
                $classroom->class_teacher_id = null;
                $classroom->save();
            }

            // Save the teacher
            if ($teacher->save()) {
                echo CJSON::encode(array('success' => true));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to save changes',
                    'errors' => $teacher->errors
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error removing class: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Manages all classes
     */
    public function actionManageClasses()
    {
        // Get all classrooms from the database
        $classrooms = ClassRoom::model()->findAll();

        // Render the view with data
        $this->render('manageclasses', array(
            'classrooms' => $classrooms
        ));
    }

    /**
     * Get classroom data for editing or viewing
     * This is an API endpoint that would be called via AJAX
     */
    public function actionGetClassroom($id)
    {
        try {
            // Find the classroom by ID
            $classroom = ClassRoom::model()->findByPk(new ObjectId($id));

            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Get teacher name if assigned
            $teacherName = 'Not Assigned';
            if (!empty($classroom->class_teacher_id)) {
                $teacher = Teacher::model()->findByPk(new ObjectId($classroom->class_teacher_id));
                if ($teacher) {
                    $teacherName = $teacher->first_name . ' ' . $teacher->last_name;
                }
            }

            // Prepare classroom data
            $classroomData = $classroom->getAttributes();

            // Ensure _id is explicitly converted to string
            $classroomData['_id'] = (string) $classroom->_id;
            $classroomData['teacherName'] = $teacherName;

            // Return classroom data
            echo CJSON::encode(array(
                'success' => true,
                'classroom' => $classroomData
            ));

        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error retrieving classroom: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Creates a new classroom
     * This is an API endpoint that would be called via AJAX
     */
    public function actionCreateClassroom()
    {
        if (isset($_POST['ClassRoom'])) {
            $classroom = new ClassRoom();
            $classroom->attributes = $_POST['ClassRoom'];

            // Initialize empty arrays
            $classroom->students = array();

            if ($classroom->save()) {
                // If a teacher was assigned, update the teacher's classes list
                if (!empty($classroom->class_teacher_id)) {
                    $teacher = Teacher::model()->findByPk(new ObjectId($classroom->class_teacher_id));
                    if ($teacher) {
                        if (!is_array($teacher->classes)) {
                            $teacher->classes = array();
                        }

                        // Convert classroom ID to string for consistent comparison
                        $classroomIdStr = (string) $classroom->_id;

                        // Check if the class is already in the teacher's classes (avoid duplicates)
                        $classExists = false;
                        foreach ($teacher->classes as $existingClassId) {
                            if ((string) $existingClassId === $classroomIdStr) {
                                $classExists = true;
                                break;
                            }
                        }

                        // Only add if it doesn't already exist
                        if (!$classExists) {
                            $teacher->classes[] = $classroomIdStr;
                            if ($teacher->save()) {
                                Yii::log("Added new class {$classroomIdStr} to teacher {$classroom->class_teacher_id}", 'info');
                            } else {
                                Yii::log("Failed to add class to teacher: " . print_r($teacher->errors, true), 'error');
                            }
                        }
                    }
                }

                echo CJSON::encode(array(
                    'success' => true,
                    'message' => 'Classroom created successfully',
                    'classroomId' => (string) $classroom->_id
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to create classroom',
                    'errors' => $classroom->errors
                ));
            }
        } else {
            echo CJSON::encode(array('success' => false, 'message' => 'No data submitted'));
        }
    }

    /**
     * Updates an existing classroom
     * This is an API endpoint that would be called via AJAX
     */
    public function actionUpdateClassroom($id)
    {
        try {
            // Find the classroom by ID
            $classroom = ClassRoom::model()->findByPk(new ObjectId($id));

            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            if (isset($_POST['ClassRoom'])) {
                // Store current teacher ID to check if it changed
                $oldTeacherId = $classroom->class_teacher_id;

                // Update classroom attributes
                $classroom->attributes = $_POST['ClassRoom'];

                // Handle teacher assignment change
                if ($oldTeacherId !== $classroom->class_teacher_id) {
                    // Remove class from old teacher if exists
                    if (!empty($oldTeacherId)) {
                        $oldTeacher = Teacher::model()->findByPk(new ObjectId($oldTeacherId));
                        if ($oldTeacher !== null && is_array($oldTeacher->classes)) {
                            // Make sure we're comparing string representations
                            $classroomIdStr = (string) $classroom->_id;
                            $classIndex = array_search($classroomIdStr, array_map('strval', $oldTeacher->classes));

                            if ($classIndex !== false) {
                                unset($oldTeacher->classes[$classIndex]);
                                $oldTeacher->classes = array_values($oldTeacher->classes); // Re-index array
                                $oldTeacher->save();
                                Yii::log("Removed class {$classroomIdStr} from teacher {$oldTeacherId}", 'info');
                            }
                        }
                    }

                    // Add class to new teacher if assigned
                    if (!empty($classroom->class_teacher_id)) {
                        $newTeacher = Teacher::model()->findByPk(new ObjectId($classroom->class_teacher_id));
                        if ($newTeacher !== null) {
                            if (!is_array($newTeacher->classes)) {
                                $newTeacher->classes = array();
                            }

                            // Make sure we're using string representation
                            $classroomIdStr = (string) $classroom->_id;

                            // Check if already in the array (using string comparison)
                            $exists = false;
                            foreach ($newTeacher->classes as $existingClassId) {
                                if ((string) $existingClassId === $classroomIdStr) {
                                    $exists = true;
                                    break;
                                }
                            }

                            if (!$exists) {
                                $newTeacher->classes[] = $classroomIdStr;
                                $newTeacher->save();
                                Yii::log("Added class {$classroomIdStr} to teacher {$classroom->class_teacher_id}", 'info');
                            }
                        }
                    }
                }

                if ($classroom->save()) {
                    echo CJSON::encode(array(
                        'success' => true,
                        'message' => 'Classroom updated successfully'
                    ));
                } else {
                    echo CJSON::encode(array(
                        'success' => false,
                        'message' => 'Failed to update classroom',
                        'errors' => $classroom->errors
                    ));
                }
            } else {
                echo CJSON::encode(array('success' => false, 'message' => 'No data submitted'));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error updating classroom: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Deletes a classroom
     * This is an API endpoint that would be called via AJAX
     */
    public function actionDeleteClassroom($id)
    {
        try {
            // Find the classroom by ID
            $classroom = ClassRoom::model()->findByPk(new ObjectId($id));

            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Remove class from teacher if assigned
            if (!empty($classroom->class_teacher_id)) {
                $teacher = Teacher::model()->findByPk(new ObjectId($classroom->class_teacher_id));
                if ($teacher !== null && is_array($teacher->classes)) {
                    $classIndex = array_search((string) $classroom->_id, $teacher->classes);
                    if ($classIndex !== false) {
                        unset($teacher->classes[$classIndex]);
                        $teacher->classes = array_values($teacher->classes); // Re-index array
                        $teacher->save();
                    }
                }
            }

            // Remove class from each student who is enrolled
            if (!empty($classroom->students) && is_array($classroom->students)) {
                foreach ($classroom->students as $studentId => $studentInfo) {
                    $student = Student::model()->findByPk(new ObjectId($studentId));
                    if ($student !== null && isset($student->classes[$classroom->class_name])) {
                        unset($student->classes[$classroom->class_name]);
                        $student->save();
                    }
                }
            }

            // Delete the classroom
            if ($classroom->delete()) {
                echo CJSON::encode(array(
                    'success' => true,
                    'message' => 'Classroom deleted successfully'
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to delete classroom'
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error deleting classroom: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Gets all students for dropdown selection
     * This is an API endpoint that would be called via AJAX
     */
    public function actionGetStudents()
    {
        try {
            // Get all students from the database
            $students = Student::model()->findAll();

            $result = array();
            foreach ($students as $student) {
                $result[] = array(
                    '_id' => (string) $student->_id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'roll_no' => $student->roll_no
                );
            }

            echo CJSON::encode(array('success' => true, 'students' => $result));
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error retrieving students: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Adds a student to a class
     * This is an API endpoint that would be called via AJAX
     */
    public function actionAddStudentToClass()
    {
        if (!isset($_POST['classroomId']) || !isset($_POST['studentId'])) {
            echo CJSON::encode(array('success' => false, 'message' => 'Missing required parameters'));
            return;
        }

        $classroomId = $_POST['classroomId'];
        $studentId = $_POST['studentId'];

        try {
            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new ObjectId($classroomId));
            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Find the student
            $student = Student::model()->findByPk(new ObjectId($studentId));
            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            // Check if the student is already in the class
            if (isset($classroom->students[$studentId])) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student is already in this class'));
                return;
            }

            // Add student to classroom
            if (!is_array($classroom->students)) {
                $classroom->students = array();
            }

            $classroom->students[$studentId] = array(
                'name' => $student->first_name . ' ' . $student->last_name,
                'roll_no' => $student->roll_no
            );

            // Add class to student
            if (!is_array($student->classes)) {
                $student->classes = array();
            }

            // Updated format to consistently include the classroom _id
            $classObjId = (string) $classroom->_id;
            $student->classes[$classroom->class_name] = array(
                '_id' => $classObjId, // Added this line to include _id explicitly
                'id' => $classObjId,  // Keep this for backward compatibility
                'subject' => $classroom->subject,
                'academic_year' => $classroom->academic_year
            );

            // Save both documents
            if ($classroom->save() && $student->save()) {
                echo CJSON::encode(array(
                    'success' => true,
                    'message' => 'Student added to class successfully'
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to add student to class',
                    'classroomErrors' => $classroom->errors,
                    'studentErrors' => $student->errors
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error adding student to class: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Removes a student from a class
     * This is an API endpoint that would be called via AJAX
     */
    public function actionRemoveStudentFromClass()
    {
        if (!isset($_POST['classroomId']) || !isset($_POST['studentId'])) {
            echo CJSON::encode(array('success' => false, 'message' => 'Missing required parameters'));
            return;
        }

        $classroomId = $_POST['classroomId'];
        $studentId = $_POST['studentId'];

        try {
            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new ObjectId($classroomId));
            if ($classroom === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Classroom not found'));
                return;
            }

            // Find the student
            $student = Student::model()->findByPk(new ObjectId($studentId));
            if ($student === null) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student not found'));
                return;
            }

            // Check if the student is in the class
            if (!isset($classroom->students[$studentId])) {
                echo CJSON::encode(array('success' => false, 'message' => 'Student is not in this class'));
                return;
            }

            // Remove student from classroom
            unset($classroom->students[$studentId]);

            // Remove class from student
            if (isset($student->classes[$classroom->class_name])) {
                unset($student->classes[$classroom->class_name]);
            }

            // Save both documents
            if ($classroom->save() && $student->save()) {
                echo CJSON::encode(array(
                    'success' => true,
                    'message' => 'Student removed from class successfully'
                ));
            } else {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => 'Failed to remove student from class',
                    'classroomErrors' => $classroom->errors,
                    'studentErrors' => $student->errors
                ));
            }
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error removing student from class: ' . $e->getMessage()
            ));
        }
    }

    public function actionGetFreeTeachers()
    {
        try {
            // Check if we need to include a specific teacher (for editing)
            $includeTeacherId = Yii::app()->request->getParam('includeTeacherId', '');

            // Get all teachers from the database
            $teachers = Teacher::model()->findAll();

            $result = array();
            foreach ($teachers as $teacher) {
                // Include teachers with no classes OR the specified teacher (when editing)
                if (empty($teacher->classes) || ($includeTeacherId && (string) $teacher->_id === $includeTeacherId)) {
                    $result[] = array(
                        '_id' => (string) $teacher->_id,
                        'first_name' => $teacher->first_name,
                        'last_name' => $teacher->last_name,
                        'email' => $teacher->email
                    );
                }
            }

            echo CJSON::encode(array('success' => true, 'teachers' => $result));
        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => 'Error retrieving free teachers: ' . $e->getMessage()
            ));
        }



    }


    public function actionAttendance()
    {


        // Get selected class ID from request
        $selectedClass = Yii::app()->request->getQuery('class_id');

        // Get all classes for this teacher
        $classes = array();
        $classrooms = ClassRoom::model()->findAll();

        foreach ($classrooms as $classroom) {
            $classes[] = array(
                'id' => (string) $classroom->_id,
                'name' => $classroom->class_name
            );
        }

        // Get students for selected class
        $students = array();
        if ($selectedClass) {
            $classroom = ClassRoom::model()->findByPk(new ObjectId($selectedClass));
            if ($classroom && isset($classroom->students) && is_array($classroom->students)) {
                foreach ($classroom->students as $studentId => $studentInfo) {
                    $student = Student::model()->findByPk(new ObjectId($studentId));
                    if ($student) {
                        $students[] = array(
                            'id' => (string) $student->_id,
                            'name' => $student->first_name . ' ' . $student->last_name,
                        );
                    }
                }
            }
        }

        $this->render('attendance', array(
            'classes' => $classes,
            'students' => $students,
            'selectedClass' => $selectedClass,
        ));
    }

    public function actionSaveAttendance()
    {
        // print_r($_POST);
        // exit;
        if (!Yii::app()->request->isPostRequest) {
            throw new CHttpException(400, 'Invalid request');
        }

        $classId = $_POST['class_id'];
        $attendanceDate = $_POST['attendance_date'];
        $attendanceData = json_decode($_POST['attendance_data'], true);

        try {
            // Find the classroom
            $classroom = ClassRoom::model()->findByPk(new ObjectId($classId));
            if (!$classroom) {
                throw new Exception('Classroom not found');
            }

            // Initialize attendance array if not exists
            if (!isset($classroom->attendance)) {
                $classroom->attendance = array();
            }

            // Create attendance record for this date
            $classroom->attendance[$attendanceDate] = array(
                'date' => $attendanceDate,
                'total_students' => count($classroom->students),
                'present' => 0,
                'absent' => 0,
                'student_records' => array()
            );

            // Process each student's attendance
            foreach ($attendanceData as $record) {
                $studentId = $record['student_id'];
                $status = $record['status'];

                // Update classroom attendance counts
                if ($status === 'present') {
                    $classroom->attendance[$attendanceDate]['present']++;
                } else {
                    $classroom->attendance[$attendanceDate]['absent']++;
                }

                // Store individual student record
                $classroom->attendance[$attendanceDate]['student_records'][$studentId] = array(
                    'status' => $status,
                    'notes' => isset($record['notes']) ? $record['notes'] : ''
                );

                // Update student's individual attendance record
                $student = Student::model()->findByPk(new ObjectId($studentId));
                if ($student) {
                    if (!isset($student->attendance)) {
                        $student->attendance = array();
                    }

                    $student->attendance[$attendanceDate] = array(
                        'class_id' => $classId,
                        'class_name' => $classroom->class_name,
                        'status' => $status,
                        'notes' => isset($record['notes']) ? $record['notes'] : ''
                    );

                    // Calculate and update attendance percentage
                    $totalRecords = count($student->attendance);
                    $presentCount = 0;
                    foreach ($student->attendance as $att) {
                        if ($att['status'] === 'present') {
                            $presentCount++;
                        }
                    }
                    $student->percentage = ($totalRecords > 0) ? round(($presentCount / $totalRecords) * 100) : 100;

                    $student->save();
                }
            }
            // print_r($classroom);
            // exit;
            // Save classroom attendance

            if ($classroom->save()) {

                echo CJSON::encode(array(
                    'success' => true,
                    'message' => 'Attendance saved successfully'
                ));
            } else {
                throw new Exception('Failed to save classroom attendance');
            }

        } catch (Exception $e) {
            echo CJSON::encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * Get attendance trend data for the last 30 days
     */
    private function getAttendanceTrendData($students)
    {
        $attendanceTrend = array();
        $currentDate = new DateTime();
        
        // Generate last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = clone $currentDate;
            $date->sub(new DateInterval("P{$i}D"));
            $dateStr = $date->format('Y-m-d');
            $dayLabel = $date->format('M j');
            
            $totalStudentsForDay = 0;
            $presentStudents = 0;
            
            // Check each student's attendance for this date
            foreach ($students as $student) {
                if (isset($student->attendance) && isset($student->attendance[$dateStr])) {
                    $attendanceRecord = $student->attendance[$dateStr];
                    
                    // Handle both array and single record formats
                    if (is_array($attendanceRecord) && isset($attendanceRecord[0])) {
                        // New format: array of attendance records
                        foreach ($attendanceRecord as $record) {
                            $totalStudentsForDay++;
                            if ($record['status'] === 'present') {
                                $presentStudents++;
                            }
                        }
                    } elseif (isset($attendanceRecord['status'])) {
                        // Old format: single attendance record
                        $totalStudentsForDay++;
                        if ($attendanceRecord['status'] === 'present') {
                            $presentStudents++;
                        }
                    }
                }
            }
            
            // Calculate percentage for this day
            $attendancePercentage = $totalStudentsForDay > 0 
                ? round(($presentStudents / $totalStudentsForDay) * 100, 1) 
                : 0;
            
            $attendanceTrend[] = array(
                'date' => $dateStr,
                'label' => $dayLabel,
                'percentage' => $attendancePercentage,
                'present' => $presentStudents,
                'total' => $totalStudentsForDay
            );
        }
        
        return $attendanceTrend;
    }

}