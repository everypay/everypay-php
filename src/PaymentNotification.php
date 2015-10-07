<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class PaymentNotification extends AbstractResource
{
    const RESOURCE_NAME = 'notifications';

    /**
     * Create a new payment notification object.
     *
     * Available params are:
     * - amount:            The amount in cents for the payment notification. [Required]
     * - payee_name:        Customer name. [Required]
     * - payee_email:       Customer email. [Required]
     * - payee_phone:       Customer phone number. [Required]
     * - description:       A decription for this payment max 255 chars. [Required]
     * - expiration_date:   After the given date, payment notification will not
     *                      be available for payment. [Optional]
     * - locale:            Send email notification to available languages
     *                      (el, en). Defaults to 'el' [Optional]
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::create($params);
    }
}
