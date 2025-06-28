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
        return !$this->isGuest && $this->getState('role') === User::ROLE_ADMIN;
    }
    
    /**
     * Check if the current user is a teacher
     * @return boolean
     */
    public function isTeacher()
    {
        return !$this->isGuest && $this->getState('role') === User::ROLE_TEACHER;
    }
    
    /**
     * Check if the current user is a student
     * @return boolean
     */
    public function isStudent()
    {
        return !$this->isGuest && $this->getState('role') === User::ROLE_STUDENT;
    }


    public function getRole(){
        return $this->getState('role');
    }

    public function getStudentClass(){
        return $this->getState('class');
    }
}
