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
     * Available params are:
     * For direct payment with credit / debit cards, card info are required.
     * - amount: The amount in cents for the payment. [Required]
     * - card_number: A valid credit /debit card number. [Required]
     * - expiration_month: Integer representation of month. [Required]
     * - expiration_month: Integer represantation of a valid expiration year. [Required]
     * - cvv: Card verification value. Three or four (American express) digits. [Required]
     * - holder_name: First and last name of the card holder. [Required]
     * For payments with card token, a valid card token required. Card tokens
     * can be obtained from Token::create api calls.
     * - token [Required].
     * Optional params.
     * - currency: The ISO 4217 code currency used for this payment. [Optional]
     * - description: A decription for this payment max 255 chars. [Optional]
     * - payee_email: Customer email. [Optional]
     * - payee_phone: Customer phone number. [Optional]
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
     * @param string|stdClass $token A valid payment token returned from a
     *                               successful payment creation.
     * @return stdClass
     */
    public static function retrieve($token)
    {
        return parent::retrieve(static::RESOURCE_NAME, $token);
    }

    /**
     * Get a collection of payment objects by applying some filters.
     * Filters are optionals and include:
     * - count: The number of objects to returns. Availabe range is 1 - 20.
     * - offset: The offset of collection to return. Useful for pagination.
     * - date_from: Return objects that created after that date.
     *   Format: YYYY-mm-dd
     * - date_to: Return objects that created before that date.
     *   Format: YYYY-mm-dd
     *
     * @param array $filters Filter options.
     * @return stdClass
     */
    public static function listAll(array $filters = array())
    {
        return parent::_listAll(static::RESOURCE_NAME, $filters);
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
        if (is_object($token)) {
            $token = $token->token;
        }

        $url      = self::getResourceUrl(static::RESOURCE_NAME) . '/refund/' . $token;
        $response = self::request($url, $params);

        return self::handleResponse($response);
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
