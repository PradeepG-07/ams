<?php

/**
 * AuthenticationHelper - Handles all authentication-related business logic
 * 
 * This class encapsulates authentication operations including:
 * - User authentication and validation
 * - Role-based access control
 * - Session management
 * - JWT token operations
 * - Password validation and security
 */
class AuthenticationHelper extends CComponent
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
     * @var array Valid user types
     */
    private static $validUserTypes = ['admin', 'teacher', 'student'];

    /**
     * Authenticate user credentials and create session
     * 
     * @param string $username User's email/username
     * @param string $password User's password
     * @return array Standardized response with authentication result
     */
    public static function authenticateUser($username, $password)
    {
        try {
            // Validate input
            $validation = self::validateAuthenticationInput($username, $password);
            if (!$validation['success']) {
                return $validation;
            }

            // Attempt authentication
            $authResult = self::performAuthentication($username, $password);
            if (!$authResult['success']) {
                return $authResult;
            }

            $userData = $authResult['data'];

            // Generate JWT token
            $tokenResult = self::generateAuthenticationToken($userData);
            if (!$tokenResult['success']) {
                return $tokenResult;
            }

            // Create user session
            $sessionResult = self::createUserSession($userData, $tokenResult['data']);
            if (!$sessionResult['success']) {
                return $sessionResult;
            }

            Yii::log('User authenticated successfully: ' . $userData['email'], 'info', 'AuthenticationHelper');

            return self::createSuccessResponse([
                'user' => $userData,
                'token' => $tokenResult['data'],
                'redirect_url' => self::getRedirectUrlForUserType($userData['user_type'])
            ], 'Authentication successful');

        } catch (Exception $e) {
            Yii::log('Authentication error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Authentication failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate authentication input parameters
     * 
     * @param string $username Username/email
     * @param string $password Password
     * @return array Validation result
     */
    private static function validateAuthenticationInput($username, $password)
    {
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username/email is required';
        } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL) && $username !== 'admin') {
            $errors[] = 'Please provide a valid email address';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 3) {
            $errors[] = 'Password must be at least 3 characters long';
        }

        if (!empty($errors)) {
            return self::createErrorResponse('Validation failed', 400, $errors);
        }

        return self::createSuccessResponse(null, 'Input validation passed');
    }

    /**
     * Perform user authentication against different user types
     * 
     * @param string $username Username/email
     * @param string $password Password
     * @return array Authentication result
     */
    private static function performAuthentication($username, $password)
    {
        // Check for admin credentials first
        $adminResult = self::authenticateAdmin($username, $password);
        if ($adminResult['success']) {
            return $adminResult;
        }

        // Try teacher authentication
        $teacherResult = self::authenticateTeacher($username, $password);
        if ($teacherResult['success']) {
            return $teacherResult;
        }

        // Try student authentication
        $studentResult = self::authenticateStudent($username, $password);
        if ($studentResult['success']) {
            return $studentResult;
        }

        return self::createErrorResponse('Invalid credentials', 401);
    }

    /**
     * Authenticate admin user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array Authentication result
     */
    private static function authenticateAdmin($username, $password)
    {
        $adminUsername = Yii::app()->params['adminUsername'] ?? 'admin';
        $adminPassword = Yii::app()->params['adminPassword'] ?? 'admin123';

        if ($username === $adminUsername && $password === $adminPassword) {
            return self::createSuccessResponse([
                'id' => 'admin',
                'email' => Yii::app()->params['adminEmail'] ?? 'admin@example.com',
                'username' => $username,
                'user_type' => 'admin',
                'name' => 'Administrator'
            ], 'Admin authentication successful');
        }

        return self::createErrorResponse('Admin authentication failed', 401);
    }

    /**
     * Authenticate teacher user
     * 
     * @param string $username Email
     * @param string $password Password
     * @return array Authentication result
     */
    private static function authenticateTeacher($username, $password)
    {
        try {
            $teacher = Teacher::model()->findByAttributes(['email' => $username]);
            
            if ($teacher && self::validatePassword($password, $teacher->password)) {
                return self::createSuccessResponse([
                    'id' => (string)$teacher->_id,
                    'email' => $teacher->email,
                    'username' => $teacher->email,
                    'user_type' => 'teacher',
                    'name' => $teacher->first_name . ' ' . $teacher->last_name,
                    'first_name' => $teacher->first_name,
                    'last_name' => $teacher->last_name
                ], 'Teacher authentication successful');
            }

            return self::createErrorResponse('Teacher authentication failed', 401);

        } catch (Exception $e) {
            Yii::log('Teacher authentication error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Teacher authentication error', 500);
        }
    }

    /**
     * Authenticate student user
     * 
     * @param string $username Email
     * @param string $password Password
     * @return array Authentication result
     */
    private static function authenticateStudent($username, $password)
    {
        try {
            $student = Student::model()->findByAttributes(['email' => $username]);
            
            if ($student && self::validatePassword($password, $student->password)) {
                return self::createSuccessResponse([
                    'id' => (string)$student->_id,
                    'email' => $student->email,
                    'username' => $student->email,
                    'user_type' => 'student',
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'roll_no' => $student->roll_no
                ], 'Student authentication successful');
            }

            return self::createErrorResponse('Student authentication failed', 401);

        } catch (Exception $e) {
            Yii::log('Student authentication error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Student authentication error', 500);
        }
    }

    /**
     * Validate password against stored hash
     * 
     * @param string $password Plain text password
     * @param string $storedPassword Hashed password from database
     * @return bool True if password is valid
     */
    private static function validatePassword($password, $storedPassword)
    {
        // For hashed passwords, use password_verify
        if (password_verify($password, $storedPassword)) {
            return true;
        }

        // Fallback for plain passwords (not recommended for production)
        if ($storedPassword === $password) {
            Yii::log('Warning: Plain text password detected. Please hash passwords for security.', 'warning', 'AuthenticationHelper');
            return true;
        }

        return false;
    }

    /**
     * Generate JWT authentication token
     * 
     * @param array $userData User data
     * @return array Token generation result
     */
    private static function generateAuthenticationToken($userData)
    {
        try {
            $token = JWTHelper::generateToken(
                $userData['id'],
                $userData['email'],
                $userData['user_type']
            );

            if (!$token) {
                return self::createErrorResponse('Failed to generate authentication token', 500);
            }

            return self::createSuccessResponse($token, 'Token generated successfully');

        } catch (Exception $e) {
            Yii::log('Token generation error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Token generation failed', 500);
        }
    }

    /**
     * Create user session with authentication data
     * 
     * @param array $userData User data
     * @param string $token JWT token
     * @return array Session creation result
     */
    private static function createUserSession($userData, $token)
    {
        try {
            // Store user data in session
            Yii::app()->user->setState('user_id', $userData['id']);
            Yii::app()->user->setState('username', $userData['username']);
            Yii::app()->user->setState('email', $userData['email']);
            Yii::app()->user->setState('user_type', $userData['user_type']);
            Yii::app()->user->setState('user_login', $userData['name']);
            Yii::app()->user->setState('jwt_token', $token);

            // Set additional session data
            Yii::app()->session['uid'] = $userData['id'];
            Yii::app()->session['login_time'] = time();

            return self::createSuccessResponse(null, 'Session created successfully');

        } catch (Exception $e) {
            Yii::log('Session creation error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Session creation failed', 500);
        }
    }

    /**
     * Get redirect URL based on user type
     * 
     * @param string $userType User type (admin, teacher, student)
     * @return string Redirect URL
     */
    private static function getRedirectUrlForUserType($userType)
    {
        switch ($userType) {
            case 'admin':
                return Yii::app()->createUrl('/admin/managestudents');
            case 'teacher':
                return Yii::app()->createUrl('/teacher/classes');
            case 'student':
                return Yii::app()->createUrl('/student/dashboard');
            default:
                return Yii::app()->createUrl('/auth/login');
        }
    }

    /**
     * Validate JWT token
     * 
     * @param string $token JWT token
     * @return array Validation result
     */
    public static function validateToken($token)
    {
        try {
            if (empty($token)) {
                return self::createErrorResponse('Token is required', 400);
            }

            $decoded = JWTHelper::validateToken($token);
            
            if (!$decoded) {
                return self::createErrorResponse('Invalid or expired token', 401);
            }

            return self::createSuccessResponse($decoded, 'Token is valid');

        } catch (Exception $e) {
            Yii::log('Token validation error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Token validation failed', 500);
        }
    }

    /**
     * Check if user has required permissions
     * 
     * @param string $userType Current user type
     * @param array $allowedTypes Array of allowed user types
     * @return array Permission check result
     */
    public static function checkPermissions($userType, $allowedTypes)
    {
        try {
            if (empty($userType)) {
                return self::createErrorResponse('User type not specified', 401);
            }

            if (!in_array($userType, self::$validUserTypes)) {
                return self::createErrorResponse('Invalid user type', 401);
            }

            if (!in_array($userType, $allowedTypes)) {
                return self::createErrorResponse('Insufficient permissions', 403);
            }

            return self::createSuccessResponse(null, 'Permission granted');

        } catch (Exception $e) {
            Yii::log('Permission check error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Permission check failed', 500);
        }
    }

    /**
     * Logout user and clean session
     * 
     * @return array Logout result
     */
    public static function logoutUser()
    {
        try {
            // Clear JWT token cookie if exists
            if (isset(Yii::app()->request->cookies['jwt_token'])) {
                unset(Yii::app()->request->cookies['jwt_token']);
            }

            // Clear user session
            Yii::app()->user->clearStates();
            Yii::app()->user->logout();

            // Clear additional session data
            if (isset(Yii::app()->session['uid'])) {
                unset(Yii::app()->session['uid']);
            }
            if (isset(Yii::app()->session['login_time'])) {
                unset(Yii::app()->session['login_time']);
            }

            Yii::log('User logged out successfully', 'info', 'AuthenticationHelper');

            return self::createSuccessResponse(null, 'Logout successful');

        } catch (Exception $e) {
            Yii::log('Logout error: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Logout failed', 500);
        }
    }

    /**
     * Hash password for storage
     * 
     * @param string $password Plain text password
     * @return array Hashing result
     */
    public static function hashPassword($password)
    {
        try {
            if (empty($password)) {
                return self::createErrorResponse('Password is required', 400);
            }

            if (strlen($password) < 6) {
                return self::createErrorResponse('Password must be at least 6 characters long', 400);
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            

            return self::createSuccessResponse($hashedPassword, 'Password hashed successfully');

        } catch (ValueError $e) {
            Yii::log('Password hashing ValueError: ' . $e->getMessage(), 'error', 'AuthenticationHelper');
            return self::createErrorResponse('Password hashing failed', 500);
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