<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Seller resource class.
 */
class Seller extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'sellers';

    /**
     * Retrieve a seller's balance.
     * 
     * @param string|stdClass $token
     * 
     * @return stdClass
     */
    public static function balance($token)
    {
        $params = array('token_id' => $token);

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
