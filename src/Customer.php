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
     * Available params are:
     * For customers from credit / debit cards, card info are required.
     * - card_number: A valid credit /debit card number. [Required]
     * - expiration_month: Integer representation of month. [Required]
     * - expiration_year: Integer represantation of a valid expiration year. [Required]
     * - cvv: Card verification value. Three or four (American express) digits. [Required]
     * - holder_name: First and last name of the card holder. [Required]
     * For customers from card token, a valid card token required. Card tokens
     * can be obtained from Token::create api calls.
     * - token [Required].
     * Optional params.
     * - description: A decription for this customer max 255 chars. [Optional]
     * - email:       Customer email. [Optional]
     * - full_name:   Customer full name. [Optional]
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
     * Parameters that can be updated are:
     * - description: A decription for this customer max 255 chars. [Optional]
     * - email:       Customer email. [Optional]
     * - full_name:   Customer full name. [Optional]
     *
     * @param string|stdClass $token A valid customer token returned from a
     *                               successful customer creation.
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
     * @param string|stdClass $token A valid customer token returned from a
     *                               successful customer creation.
     * @return stdClass
     */
    public static function delete($token)
    {
        return parent::delete($token);
    }
}
