<?php

namespace Everypay;

class Iris extends AbstractResource
{
    const RESOURCE_NAME = 'iris';

    /**
     * Not available for this resource.
     *
     * @throws Everypay\Exception\RuntimeException
     */
    public static function create(array $params)
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(static::RESOURCE_NAME) .
            ' does not support method ' . __METHOD__
        );    }

    /**
     * Not available for this resource.
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

    /**
     * Not available for this resource.
     *
     * @param string|stdClass
     * @param array $params
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
     * Create a new iris session object.
     *
     *  Available params are:
     *  - amount:            The amount in cents for transaction. [Required]
     *  - currency:          The currency of the transaction. Currently only EUR is supported. [Required]
     *  - country:           The country code of the transaction. Currently only GR is supported. [Required]
     *  - callback_url:      Endpoint where EveryPay will send results. [Required]
     *  - md:                Merchant data, max 255 characters. Returned in callback. [Optional]
     *  - uuid:              Unique session attempt's id. [Optional]
     *
     * @param array $params
     * @return stdClass
     */
    public static function session(array $params = array())
    {
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }
}
