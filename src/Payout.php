<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Payout resource class.
 */
class Payout extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'payouts';

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
