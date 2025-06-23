<?php

/**
 * ValidationHelper - Handles all validation-related business logic
 * 
 * This class provides centralized validation functions for:
 * - Input data validation
 * - Model validation
 * - Business rule validation
 * - Data sanitization
 * - Error formatting
 */
class ValidationHelper extends CComponent
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
     * Validate email address
     * 
     * @param string $email Email to validate
     * @param bool $required Whether email is required
     * @return array Validation result
     */
    public static function validateEmail($email, $required = true)
    {
            $errors = [];
            
            // Trim whitespace from email
            $email = trim($email);

            if ($required && empty($email)) {
                $errors[] = 'Email address is required';
            } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please provide a valid email address';
            }

            if (!empty($errors)) {
                return self::createErrorResponse('Email validation failed', 400, $errors);
            }

            return self::createSuccessResponse($email, 'Email validation passed');

    }

    /**
     * Validate password strength
     * 
     * @param string $password Password to validate
     * @param int $minLength Minimum password length
     * @param bool $requireSpecialChars Whether to require special characters
     * @return array Validation result
     */
    public static function validatePassword($password, $minLength = 6, $requireSpecialChars = false)
    {
            $errors = [];

            if (empty($password)) {
                $errors[] = 'Password is required';
            } else {
                if (strlen($password) < $minLength) {
                    $errors[] = "Password must be at least {$minLength} characters long";
                }

                if ($requireSpecialChars) {
                    if (!preg_match('/[A-Z]/', $password)) {
                        $errors[] = 'Password must contain at least one uppercase letter';
                    }
                    if (!preg_match('/[a-z]/', $password)) {
                        $errors[] = 'Password must contain at least one lowercase letter';
                    }
                    if (!preg_match('/[0-9]/', $password)) {
                        $errors[] = 'Password must contain at least one number';
                    }
                    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                        $errors[] = 'Password must contain at least one special character';
                    }
                }
            }

            if (!empty($errors)) {
                return self::createErrorResponse('Password validation failed', 400, $errors);
            }

            return self::createSuccessResponse(null, 'Password validation passed');

    }

    /**
     * Validate name fields (first name, last name)
     * 
     * @param string $name Name to validate
     * @param string $fieldName Field name for error messages
     * @param bool $required Whether field is required
     * @return array Validation result
     */
    public static function validateName($name, $fieldName = 'Name', $required = true)
    {
            $errors = [];

            if ($required && empty($name)) {
                $errors[] = "{$fieldName} is required";
            } elseif (!empty($name)) {
                $name = trim($name);
                
                if (strlen($name) < 2) {
                    $errors[] = "{$fieldName} must be at least 2 characters long";
                } elseif (strlen($name) > 50) {
                    $errors[] = "{$fieldName} must not exceed 50 characters";
                } elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $name)) {
                    $errors[] = "{$fieldName} can only contain letters, spaces, hyphens, apostrophes, and periods";
                }
            }

            if (!empty($errors)) {
                return self::createErrorResponse('Name validation failed', 400, $errors);
            }

            return self::createSuccessResponse(trim($name), 'Name validation passed');

    }

    /**
     * Validate date format and range
     * 
     * @param string $date Date to validate
     * @param string $format Expected date format (default: Y-m-d)
     * @param bool $allowFuture Whether future dates are allowed
     * @param bool $required Whether date is required
     * @return array Validation result
     */
    public static function validateDate($date, $format = 'Y-m-d', $allowFuture = true, $required = true)
    {
            $errors = [];

            if ($required && empty($date)) {
                $errors[] = 'Date is required';
            } elseif (!empty($date)) {
                $dateObj = DateTime::createFromFormat($format, $date);
                
                if (!$dateObj || $dateObj->format($format) !== $date) {
                    $errors[] = "Date must be in {$format} format";
                } else {
                    $now = new DateTime();
                    
                    if (!$allowFuture && $dateObj > $now) {
                        $errors[] = 'Future dates are not allowed';
                    }
                    
                    // Check for reasonable date range (not too far in past/future)
                    $minDate = new DateTime('1900-01-01');
                    $maxDate = new DateTime('+10 years');
                    
                    if ($dateObj < $minDate || $dateObj > $maxDate) {
                        $errors[] = 'Date is outside acceptable range';
                    }
                }
            }

            if (!empty($errors)) {
                return self::createErrorResponse('Date validation failed', 400, $errors);
            }

            return self::createSuccessResponse($date, 'Date validation passed');

    }

    /**
     * Validate MongoDB ObjectId
     * 
     * @param string $id ObjectId to validate
     * @param string $fieldName Field name for error messages
     * @param bool $required Whether ID is required
     * @return array Validation result
     */
    public static function validateObjectId($id, $fieldName = 'ID', $required = true)
    {
            $errors = [];

            if ($required && empty($id)) {
                $errors[] = "{$fieldName} is required";
            } elseif (!empty($id)) {
                // Check if it's a valid ObjectId format (24 character hex string)
                if (!preg_match('/^[a-f\d]{24}$/i', $id)) {
                    $errors[] = "{$fieldName} must be a valid ObjectId";
                }
            }

            if (!empty($errors)) {
                return self::createErrorResponse('ObjectId validation failed', 400, $errors);
            }

            return self::createSuccessResponse($id, 'ObjectId validation passed');

    }

    /**
     * Validate attendance status
     * 
     * @param string $status Status to validate
     * @param bool $required Whether status is required
     * @return array Validation result
     */
    public static function validateAttendanceStatus($status, $required = true)
    {
            $errors = [];
            $validStatuses = ['present', 'absent', 'late'];

            if ($required && empty($status)) {
                $errors[] = 'Attendance status is required';
            } elseif (!empty($status) && !in_array($status, $validStatuses)) {
                $errors[] = 'Attendance status must be one of: ' . implode(', ', $validStatuses);
            }

            if (!empty($errors)) {
                return self::createErrorResponse('Attendance status validation failed', 400, $errors);
            }

            return self::createSuccessResponse($status, 'Attendance status validation passed');

    }

    /**
     * Validate user type
     * 
     * @param string $userType User type to validate
     * @param bool $required Whether user type is required
     * @return array Validation result
     */
    public static function validateUserType($userType, $required = true)
    {
            $errors = [];
            $validTypes = ['admin', 'teacher', 'student'];

            if ($required && empty($userType)) {
                $errors[] = 'User type is required';
            } elseif (!empty($userType) && !in_array($userType, $validTypes)) {
                $errors[] = 'User type must be one of: ' . implode(', ', $validTypes);
            }

            if (!empty($errors)) {
                return self::createErrorResponse('User type validation failed', 400, $errors);
            }

            return self::createSuccessResponse($userType, 'User type validation passed');

    }

    /**
     * Sanitize input string
     * 
     * @param string $input Input to sanitize
     * @param bool $allowHtml Whether to allow HTML tags
     * @return array Sanitization result
     */
    public static function sanitizeInput($input, $allowHtml = false)
    {
            if (is_null($input)) {
                return self::createSuccessResponse('', 'Input sanitized');
            }

            $sanitized = trim($input);

            if (!$allowHtml) {
                // First remove potentially dangerous script content
                $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $sanitized);
                
                // Remove all HTML tags FIRST
                $sanitized = strip_tags($sanitized);
                
                // Then convert special characters to HTML entities
                $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
            } else {
                // If HTML is allowed, still remove dangerous tags
                $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $sanitized);
            }

            // Remove null bytes
            $sanitized = str_replace("\0", '', $sanitized);

            return self::createSuccessResponse($sanitized, 'Input sanitized successfully');

    }

    /**
     * Validate array of attendance records
     * 
     * @param array $attendanceData Array of attendance records
     * @return array Validation result
     */
    public static function validateAttendanceData($attendanceData)
    {
            $errors = [];

            if (!is_array($attendanceData)) {
                $errors[] = 'Attendance data must be an array';
                return self::createErrorResponse('Attendance data validation failed', 400, $errors);
            }

            if (empty($attendanceData)) {
                $errors[] = 'Attendance data cannot be empty';
                return self::createErrorResponse('Attendance data validation failed', 400, $errors);
            }

            foreach ($attendanceData as $index => $record) {
                if (!is_array($record)) {
                    $errors[] = "Record at index {$index} must be an array";
                    continue;
                }

                // Validate student_id
                if (!isset($record['student_id']) || empty($record['student_id'])) {
                    $errors[] = "Student ID is required for record at index {$index}";
                } else {
                    $idValidation = self::validateObjectId($record['student_id'], 'Student ID', true);
                    if (!$idValidation['success']) {
                        $errors[] = "Invalid student ID for record at index {$index}";
                    }
                }

                // Validate status
                if (!isset($record['status']) || empty($record['status'])) {
                    $errors[] = "Status is required for record at index {$index}";
                } else {
                    $statusValidation = self::validateAttendanceStatus($record['status'], true);
                    if (!$statusValidation['success']) {
                        $errors[] = "Invalid status for record at index {$index}";
                    }
                }

                // Validate notes (optional)
                if (isset($record['notes']) && !is_string($record['notes'])) {
                    $errors[] = "Notes must be a string for record at index {$index}";
                }
            }

            if (!empty($errors)) {
                return self::createErrorResponse('Attendance data validation failed', 400, $errors);
            }

            return self::createSuccessResponse($attendanceData, 'Attendance data validation passed');

    }

    /**
     * Validate model using Yii's built-in validation
     * 
     * @param CActiveRecord $model Model to validate
     * @return array Validation result
     */
    public static function validateModel($model)
    {
            if (!$model instanceof CActiveRecord) {
                return self::createErrorResponse('Invalid model provided', 400);
            }

            if ($model->validate()) {
                return self::createSuccessResponse($model, 'Model validation passed');
            }

            $errors = [];
            foreach ($model->getErrors() as $attribute => $attributeErrors) {
                foreach ($attributeErrors as $error) {
                    $errors[] = $error;
                }
            }

            return self::createErrorResponse('Model validation failed', 400, $errors);

    }

    /**
     * Create standardized success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @return array Standardized response
     */
    private static function createSuccessResponse($data = null, $message = 'Validation successful')
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
    private static function createErrorResponse($message = 'Validation failed', $code = 400, $errors = [])
    {
        return array_merge(self::$responseTemplate, [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors
        ]);
    }
}