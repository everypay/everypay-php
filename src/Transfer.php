<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Transfer resource class.
 */
class Transfer extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'transfers';

    /**
     * Create a payment transfer.
     * 
     * @param string|stdClass $token
     * @param array $params
     * 
     * @return stdClass
     */
    public static function payment($token, array $params)
    {
        $params['token_id'] = $token;

        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Create a direct transfer.
     * 
     * @param string|stdClass $token
     * @param array $params
     * 
     * @return stdClass
     */
    public static function direct($token, array $params)
    {
        $params['token_id'] = $token;

        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Not avalable for this resource.
     *
     * @throws Everypay\Exception\RuntimeException
     */
    public static function delete($token, array $params = array())
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(static::RESOURCE_NAME) .
            ' does not support method ' . __METHOD__
        );
    }
}
