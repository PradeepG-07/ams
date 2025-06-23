<?php

/**
 * WebUser component for handling user type-specific operations
 */
class WebUser extends CWebUser
{
    /**
     * Check if the current user is an admin
     * @return boolean
     */
    public function isAdmin()
    {
        return !$this->isGuest && $this->getState('user_type') === 'admin';
    }
    
    /**
     * Check if the current user is a teacher
     * @return boolean
     */
    public function isTeacher()
    {
        return !$this->isGuest && $this->getState('user_type') === 'teacher';
    }
    
    /**
     * Check if the current user is a student
     * @return boolean
     */
    public function isStudent()
    {
        return !$this->isGuest && $this->getState('user_type') === 'student';
    }
    
    /**
     * Get the user type
     * @return string|null
     */
    public function getUserType()
    {
        return $this->getState('user_type');
    }
}
