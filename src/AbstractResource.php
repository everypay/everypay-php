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

    private static $actions = array(
        'create',
        'capture',
        'retrieve',
        'listAll',
        'refund',
        'update',
        'delete'
    );

    private static $resources = array(
        'tokens',
        'payments',
        'customers'
    );

    private static $clientOptions = array();

    /**
     * Create a new resource object.
     * See Resource class for more input info.
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Retrieve an existing resource based on its token.
     *
     * @param string|stdClass $token A valid resource token returned from a
     *                               successful resource creation.
     * @return stdClass
     */
    public static function retrieve($token)
    {
        $params = array('token_id' => $token);
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Get a collection of given resource objects by applying some filters.
     * Filters are optionals and include:
     * - count:     The number of objects to returns. Availabe range is 1 - 20.
     * - offset:    The offset of collection to return. Useful for pagination.
     * - date_from: Return objects that created after that date.
     *              Format: YYYY-mm-dd
     * - date_to:   Return objects that created before that date.
     *              Format: YYYY-mm-dd
     *
     * @param array $filters Filter options.
     * @return stdClass
     */
    public static function listAll(array $filters = array())
    {
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $filters);
    }

    /**
     * Update an existing resource.
     *
     * @param string|stdClass $token
     * @param array $params
     * @return stdClass
     */
    public static function update($token, array $params)
    {
        $params['token_id'] = $token;
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Delete a resource.
     *
     * @param string|stdClass $token
     * @return stdClass
     */
    public static function delete($token)
    {
        $params = array('token_id' => $token);
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    public static function setClientOption($option, $value)
    {
        self::$clientOptions[$option] = $value;
    }

    public function setClient(ClientInterface $client)
    {
        self::$client = $client;
    }

    protected static function invoke($action, $resourceName, array $params = array())
    {
        if (!in_array($action, self::$actions)) {
            throw new Exception\InvalidArgumentException(sprintf("Action %s is not exists", $action));
        }

        if (!in_array($resourceName, self::$resources)) {
            throw new Exception\InvalidArgumentException(sprintf("Resource %s is not exists", $resourceName));
        }

        $options = array(
            'resource' => $resourceName,
            'api_key'  => Everypay::getApiKey(),
            'api_uri'  => Everypay::getApiUrl(),
        );

        $options = array_merge($params, $options);
        $actionClass = 'Everypay\\Action\\' . (ucwords($action));
        $actionInstance = new $actionClass($options);
        $request = $actionInstance->__invoke();
        return self::handleResponse(self::createClient()->send($request));
    }

    private static function createClient()
    {
        $client = self::$client ?: new CurlClient(self::$clientOptions);
        $client->setOption(CurlClient::TIMEOUT, 30);
        $client->setOption(CurlClient::USER_AGENT, 'EveryPay PHP Library ' . Everypay::VERSION);

        return $client;
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
        $contentType = $response->getHeaderLine('Content-Type');
        if (stripos($contentType, 'application/json') === false) {
            throw new Exception\CurlException(
                'The returned response is not in json format'
            );
        }

        $response = json_decode($response->getBody());

        if (isset($response->error->code)) {
            if (EveryPay::throwExceptions()) {
                throw new Everypay_Exception_ApiErrorException(
                    $response->error->message, $response->error->code
                );
            }
        }

        return $response;
    }
}
