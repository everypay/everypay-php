<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Customer resource class.
 */
class Customer extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'customers';

    /**
     * Create a new customer object.
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::create($params);
    }

    /**
     * Update an existing customer.
     *
     * @param string|stdClass $token
     * @param array $params
     * @return stdClass
     */
    public static function update($token, array $params)
    {
        return parent::update($token, $params);
    }

    /**
     * Delete a customer.
     *
     * @param string|stdClass $token
     * @return stdClass
     */
    public static function delete($token)
    {
        return parent::delete($token);
    }
}
