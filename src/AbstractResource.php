<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Common methods for all API resources.
 */
abstract class AbstractResource
{
    /**
     * Create a new object.
     * 
     * @param string $resource
     * @param array  $params
     */
    public static function create($resource, array $params)
    {
        $response = self::request(self::getResourceUrl($resource), $params);
        
        return self::handleResponse($response);
    }
    
    /**
     * Retrieve an existing object based on his token.
     * 
     * @param string $resource
     * @param string|stdClass $token
     */
    public static function retrieve($resource, $token)
    {
        if (is_object($token)) {
            $token = $token->token;
        }
        
        $url      = self::getResourceUrl($resource) . '/' . $token;
        $response = self::request($url, array(), 'GET');
        
        return self::handleResponse($response);
    }
    
    /**
     * List all objects for a resource.
     * 
     * @param string $resource
     * @param array $params
     * @return stdClass
     */
    protected static function _listAll($resource, array $params = array())
    {
        $response = self::request(self::getResourceUrl($resource), $params, 'GET');
        
        return self::handleResponse($response);
    }
    
    /**
     * Update an object based on his token.
     * 
     * @param string $resource
     * @param string|stdClass $token
     * @param array $params
     * @return stdClass
     */
    protected static function _update($resource, $token, array $params)
    {
        if (is_object($token)) {
            $token = $token->token;
        }
        
        $url      = self::getResourceUrl($resource) . '/' . $token;
        $response = self::request($url, $params);
        
        return self::handleResponse($response);
    }
    
    /**
     * Delete an object.
     * 
     * @param string $resource
     * @param string|stdClass $token
     * @return stdClass
     */
    protected static function _delete($resource, $token)
    {
        if (is_object($token)) {
            $token = $token->token;
        }
        
        $url      = self::getResourceUrl($resource) . '/' . $token;
        $response = self::request($url, array(), 'DELETE');
        
        return self::handleResponse($response);
    }
    
    /**
     * Handle API response.
     * 
     * @param stdClass $response
     * @return stdClass
     * @throws Everypay_Exception_ApiErrorException
     */
    protected static function handleResponse($response)
    {
        if (isset($response->error->code)) {
            if (EveryPay::throwExceptions()) {
                throw new Everypay_Exception_ApiErrorException(
                    $response->error->message, $response->error->code
                );
            }
        }
        
        return $response;
    }
    
    /**
     * Return the API resource URI.
     * 
     * @param string $resource
     * @return string
     */
    public static function getResourceUrl($resource)
    {
        return Everypay::getApiUrl() . '/' . $resource;
    }
    
    /**
     * Make an API request with curl.
     *
     * @param  string $url
     * @param  array  $params
     * @param  string $method
     * @return array
     */
    protected static function request($url, array $params = array(), $method = 'POST')
    {
        $curl   = curl_init();
        $apiKey = Everypay::getApiKey();
        
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'User-Agent: EveryPay PHP Library ' . Everypay::VERSION
        ));

        // HTTP Auth Basic
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $apiKey . ':');
        
        if (!empty($params)) {
            $query = http_build_query($params, null, '&');
            if ('get' === strtolower($method)) {
                $url .= (false === strpos($url, '?')) ? '?' : '&';
                $url .= $query;
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($curl);
        $info     = curl_getinfo($curl);
        
        if (curl_errno($curl)) {
            throw new Exception\CurlException(curl_error($curl));
        }
        
        curl_close($curl);
        
        if (stripos($info['content_type'], 'application/json') === false) {
            throw new Everypay_Exception_CurlException(
                'The returned response is not in json format'
            );
        }
        
        return json_decode($response);
    }
}