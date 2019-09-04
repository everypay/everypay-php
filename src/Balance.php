<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Balance resource class.
 */
class Balance extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'balance';

    /**
     * Retrieve a merchant's balance.
     * 
     * @return stdClass
     */
    public static function balance()
    {
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, array());
    }

    /**
     * Not avalable for this resource.
     *
     * @throws Everypay\Exception\RuntimeException
     */
    public static function create(array $params)
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(static::RESOURCE_NAME) .
            ' does not support method ' . __METHOD__
        );
    }

    /**
     * Not avalable for this resource.
     *
     * @throws Everypay\Exception\RuntimeException
     */
    public static function update($token, array $params)
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(static::RESOURCE_NAME) .
            ' does not support method ' . __METHOD__
        );
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
