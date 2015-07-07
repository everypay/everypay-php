<?php
/**
 * EveryPay PHP Library
 */

namespace Everypay;

/**
 * Payment resource class.
 */
class Payment extends AbstractResource
{
    /**
     * API resource name.
     *
     * @var string
     */
    const RESOURCE_NAME = 'payments';

    /**
     * Create a new payment object.
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::create(static::RESOURCE_NAME, $params);
    }

    /**
     * Retrieve an existing payment based on his token.
     *
     * @param string|stdClass $token
     * @return stdClass
     */
    public static function retrieve($token)
    {
        return parent::_retrieve(self::getResourceName(), $token);
    }

    /**
     * Get a list with payment objects.
     *
     * @param array $params
     * @return array
     */
    public static function listAll(array $params = array())
    {
        return parent::_listAll(self::getResourceName(), $params);
    }

    /**
     * Refund a payment.
     *
     * @param type $token
     * @param array $params
     */
    public static function refund($token, array $params = array())
    {
        if (is_object($token)) {
            $token = $token->token;
        }

        $url      = self::getResourceUrl(self::getResourceName()) . '/refund/' . $token;
        $response = self::request($url, $params);

        return self::handleResponse($response);
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

    /**
     * Not avalable for this resource.
     *
     * @param string|stdClass
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
}
