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
     * Available params are:
     * - amount: The amount in cents for the payment. [Required]
     * For direct payment with credit / debit cards, card info are required.
     * - card_number:       A valid credit /debit card number. [Required]
     * - expiration_month:  Integer representation of month. [Required]
     * - expiration_year:   Integer represantation of a valid expiration year. [Required]
     * - cvv:               Card verification value. Three or four (American express) digits. [Required]
     * - holder_name:       First and last name of the card holder. [Required]
     * For payments with card token, a valid card token required. Card tokens
     * can be obtained from Token::create api calls.
     * - token [Required].
     * Optional params.
     * - currency:      The ISO 4217 code currency used for this payment. [Optional]
     * - description:   A decription for this payment max 255 chars. [Optional]
     * - payee_email:   Customer email. [Optional]
     * - payee_phone:   Customer phone number. [Optional]
     * - capture:       Boolean Whether to capture a payment or just authorize it.
     *                  To authorize a payment this value must be 0.
     * - create_customer: Boolean Whether to create a customer and store its
     *                    card or not.
     * - installments: Integer The number of installments for this payment.
     *                 Can only be used for credit card payments.
     * - max_installments: Integer Used for payments with token, to validate
     *                      max installments set by the merchant from
     *                      everypay Button.
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::create($params);
    }

    public static function capture($token)
    {
        $params['token_id'] = $token;
        return parent::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Refund a payment.
     * Available $params are:
     * - amount: The amount to refund. The amount must not exceed the maximum
     *           amount of Payment. Can be used for partial refunds. If ommited
     *           then a full refund will be created. [Optional]
     * - description: A description for this refund max 255 chars. [Optional]
     *
     * @param string|stdClass $token A valid payment token returned from a
     *                               successful payment creation.
     * @param array $params
     */
    public static function refund($token, array $params = array())
    {
        $params['token_id'] = $token;
        return parent::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    /**
     * Not available for this resource.
     *
     * @param array $params
     * @throws Everypay\Exception\RuntimeException
     */
    public static function delete($token)
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
