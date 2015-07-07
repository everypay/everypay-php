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
     * {@inheritdoc}
     */
    public static function getResourceName()
    {
        return self::RESOURCE_NAME;
    }

    /**
     * Create a new customer object.
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::_create(self::getResourceName(), $params);
    }

    /**
     * Retrieve an existing customer based on his token.
     *
     * @param string|stdClass $token
     * @return stdClass
     */
    public static function retrieve($token)
    {
        return parent::_retrieve(self::getResourceName(), $token);
    }

    /**
     * Get a list with customer objects.
     *
     * @param array $params
     * @return array
     */
    public static function listAll(array $params = array())
    {
        return parent::_listAll(self::getResourceName(), $params);
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
        return parent::_update(self::getResourceName(), $token, $params);
    }

    /**
     * Delete a customer.
     *
     * @param string|stdClass $token
     * @return stdClass
     */
    public static function delete($token)
    {
        return parent::_delete(self::getResourceName(), $token);
    }
}
