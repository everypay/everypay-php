<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

use Everypay\Http\Client\CurlClient;
use Everypay\Http\Client\ClientInterface;

/**
 * Common methods for all API resources.
 */
abstract class AbstractResource
{
    private static $client;

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
        $client = self::getClient() ?: new CurlClient();
        $client->setOption(CurlClient::TIMEOUT, 30);
        $client->setOption(CurlClient::USER_AGENT, 'EveryPay PHP Library ' . Everypay::VERSION);
        $client->setOption(CurlClient::SSL_VERIFY_PEER, false);

        $request = new Http\Request();
        $uri = new Http\Uri($url);
        $uri = $uri->withUserInfo(Everypay::getApiKey());
        $request = $request->withUri($uri)
            ->withMethod($method)
            ->withBody($client->createStreamFromArray($params));
        $response = $client->send($request);

        $contentType = $response->getHeaderLine('Content-Type');
        if (stripos($contentType, 'application/json') === false) {
            throw new Exception\CurlException(
                'The returned response is not in json format'
            );
        }

        return json_decode($response->getBody());
    }

    public static function setClient(ClientInterface $client)
    {
        self::$client = $client;
    }

    public function getClient()
    {
        return self::$client;
    }
}
