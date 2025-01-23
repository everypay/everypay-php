<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class IssuerLoyalty extends AbstractResource
{
    const RESOURCE_NAME = 'issuer-loyalty';

    /**
     * Create a new issuer loyalty information object.
     *
     * Available params are:
     * - amount:            The amount in cents for transaction (whole: financial+loyalty). [Required]
     * - card_number:       Card number (requires a PCI DSS SAQ D Certification). [Optional]
     * - customer_token:    Customer token. [Optional]
     * - card_token:        Card token. [Optional]
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::create($params);
    }

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
}
