<?php

/**
 * BaseHelper - Base helper class providing common functionality
 * 
 * This class serves as a foundation for other helper classes and provides
 * common utility methods and functionality.
 */
class BaseHelper extends CComponent
{
    /**
     * @var array Common constants
     */
    const VERSION = '1.0.0';
    const DEFAULT_ENCODING = 'UTF-8';
    
    /**
     * Get application version
     * 
     * @return string Version string
     */
    public static function getVersion()
    {
        return self::VERSION;
    }
    
    /**
     * Get default encoding
     * 
     * @return string Encoding
     */
    public static function getEncoding()
    {
        return self::DEFAULT_ENCODING;
    }
    
    /**
     * Check if helper is available
     * 
     * @return bool Always true for base helper
     */
    public static function isAvailable()
    {
        return true;
    }
    
    /**
     * Get current timestamp
     * 
     * @return int Current timestamp
     */
    public static function getCurrentTimestamp()
    {
        return time();
    }
    
    /**
     * Check if running in debug mode
     * 
     * @return bool True if in debug mode
     */
    public static function isDebugMode()
    {
        return defined('YII_DEBUG') && YII_DEBUG;
    }
}
