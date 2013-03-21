<?php
/**
 * EveryPay PHP Library
 */

require_once 'Everypay/Exception/RuntimeException.php';
require_once 'Everypay/Exception/InvalidArgumentException.php';

/**
 * EveryPay configuration class.
 */
final class EveryPay
{
    /**
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * API request key.
     * 
     * @var string
     */
    private static $apiKey = null;
    
    /**
     * EveryPay API url.
     * 
     * @var string
     */
    private static $apiUrl = 'http://api.everypay.local';
    
    /**
     * Whether or not to throw an exception if API has returned an error.
     * 
     * @var boolean 
     */
    private static $throwExceptions = true;
    
    /**
     * Check for needed requirements.
     */
    public static function checkRequirements()
    {
        $extensions = array('curl', 'json');
        
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                throw new Everypay_Exception_RuntimeException(
                    'You need the PHP ' . $extension
                    . ' extension in order to use EveryPay PHP Library'
                );
            }
        }
    }
    
    /**
     * Set the throwExceptions flag and retrieve current status
     *
     * Set whether API errors should throw an exception.
     *
     * @param boolean $flag Defaults to null (return flag state)
     * @return boolean
     */
    public static function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            self::$throwExceptions = (bool) $flag;
        }

        return self::$throwExceptions;
    }
    
    /**
     * Set an API key for the request.
     * 
     * @param string $key
     */
    public static function setApiKey($key)
    {
        self::$apiKey = (string) $key;
    }
    
    /**
     * Get the API Key.
     * 
     * @return string
     * @throws Everypay_Exception_RuntimeException
     */
    public static function getApiKey()
    {
        if (self::$apiKey === null) {
            throw new Everypay_Exception_RuntimeException(
                "You must set first an API key in order to continue."
            );
        }
        
        return self::$apiKey;
    }
    
    /**
     * Set the API url for the request.
     * 
     * @param string $url
     * @throws Everypay_Exception_InvalidArgumentException
     */
    public static function setApiUrl($url)
    {
        $apiUrl = filter_var($url, FILTER_VALIDATE_URL);
        
        if (!$apiUrl) {
            throw new Everypay_Exception_InvalidArgumentException(
                'API URL should be a valid url'
            );
        }
        
        self::$apiUrl = rtrim($url, '\\/');
    }
    
    /**
     * Get the API url.
     * 
     * @return string
     */
    public static function getApiUrl()
    {
        return self::$apiUrl;
    }
}
