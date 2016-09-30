<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class Schedule extends AbstractResource
{
    const RESOURCE_NAME = 'schedules';

    /**
     * Creates a new Schedule.
     *
     * Available params are:
     * - amount: The amount in cents for the payment. [Required]
     * - card_number:       A valid credit /debit card number. [Required]
     * - expiration_month:  Integer representation of month. [Required]
     * - expiration_year:   Integer represantation of a valid expiration year. [Required]
     * - cvv:               Card verification value. Three or four (American express) digits. [Required]
     * - holder_name:       First and last name of the card holder. [Required]
     * - description:       A string 255 chars max for schedule description. [Required]
     * - schedule_type:     String, one of "percentage" or "amount" [Required]
     * - schedule_rates:    Percentage rates separated with ";" when
     *                      schedule_type is "percentage". Each rate must be an
     *                      integer with sum not over 100. [Required]
     * - schedule_dates:    Dates separated with ";" char. Dates must be in ISO 8601 format. [Required]
     * - customer_email:    A valid email address for customer [Required]
     *
     * @param array $params
     * @return stdClass
     */
    public static function create(array $params)
    {
        return parent::create($params);
    }

    /**
     * Updates an existing schedule.
     *
     * Available params are:
     * - description:       A string 255 chars max for schedule description. [Required]
     * - schedule_type:     String, one of "percentage" or "amount" [Required]
     * - schedule_rates:    Percentage rates separated with ";" when
     *                      schedule_type is "percentage". Each rate must be an
     *                      integer with sum not over 100. [Required]
     * - schedule_dates:    Dates separated with ";" char. Dates must be in ISO 8601 format. [Required]
     *
     * {@inheritdoc}
     *
     */
    public static function update($token, array $params)
    {
        return parent::update($token, $params);
    }
}
