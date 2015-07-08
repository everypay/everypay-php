<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Token resource class.
 */
class Token extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'tokens';

    /**
     * Create a new card token object.
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
     * @param array $params
     * @throws Everypay\Exception\RuntimeException
     */
    public static function listAll(array $params = array())
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(self::getResourceName()) .
            ' does not support method ' . __METHOD__
        );
    }

    /**
     * Not avalable for this resource.
     *
     * @param array $params
     * @throws Everypay\Exception\RuntimeException
     */
    public static function update($token, array $params)
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(self::getResourceName()) .
            ' does not support method ' . __METHOD__
        );
    }

    /**
     * Not avalable for this resource.
     *
     * @param array $params
     * @throws Everypay\Exception\RuntimeException
     */
    public static function delete($token)
    {
        throw new Exception\RuntimeException(
            'Resource ' . ucfirst(self::getResourceName()) .
            ' does not support method ' . __METHOD__
        );
    }
}
