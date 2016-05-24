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
     * Available params are valid card info data.
     * - card_number:       A valid credit /debit card number. [Required]
     * - expiration_month:  Integer representation of month. [Required]
     * - expiration_year:   Integer represantation of a valid expiration year. [Required]
     * - cvv:               Card verification value. Three or four (American express) digits. [Required]
     * - holder_name:       First and last name of the card holder. [Required]
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
            'Resource ' . ucfirst(static::RESOURCE_NAME) .
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
