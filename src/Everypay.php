<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * EveryPay configuration class.
 */
class Everypay
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
    protected static $apiKey = null;

    /**
     * EveryPay API url.
     *
     * @var string
     */
    protected static $apiUrl = 'https://api.everypay.gr';

    /**
     * Check for needed requirements.
     *
     * @throws Everypay\Exception\RuntimeException
     */
    public static function checkRequirements()
    {
        $extensions = array('curl', 'json');

        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                throw new Exception\RuntimeException(
                    'You need the PHP ' . $extension
                    . ' extension in order to use EveryPay PHP Library'
                );
            }
        }
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
     * @throws Everypay\Exception\RuntimeException
     */
    public static function getApiKey()
    {
        if (self::$apiKey === null) {
            throw new Exception\RuntimeException(
                "You must set first an API key."
            );
        }

        return self::$apiKey;
    }

    /**
     * Set the API url for the request.
     *
     * @param string $url
     * @throws Everypay\Exception\InvalidArgumentException
     */
    public static function setApiUrl($url)
    {
        $apiUrl = filter_var($url, FILTER_VALIDATE_URL);

        if (!$apiUrl) {
            throw new Exception\InvalidArgumentException(
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
