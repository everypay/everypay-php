<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

use DateTime;

class ScheduleTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
        Everypay::$isTest = false;
    }

    public function testScheduleCreate()
    {

        $this->mockResponse($this->successScheduleCreateResponse());

        $dates = array(
            (new DateTime())->modify('1 week')->format('c'),
            (new DateTime())->modify('1 month')->format('c'),
            (new DateTime())->modify('2 month')->format('c'),
        );
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 200000,
            'description'       => 'test schedule',
            'schedule_type'     => 'percentage',
            'schedule_rates'    => '20;30;50',
            'schedule_dates'    => implode(';', $dates),
            'customer_email'    => 'test@example.com',
        );

        $schedule = Schedule::create($params);
    }

    private function successScheduleCreateResponse()
    {
        return '{ "token": "sch_mLO8VebKLFqjWY0Rc4MgXSV9", "status": "Pending schedule", "amount": "200000", "currency": "eur", "description": "test schedule", "date_created": "2016-09-30T17:29:52+0300", "schedule_type": "percentage", "customer": { "description": "test schedule", "email": "test@example.com", "date_created": "2016-09-30T17:29:52+0300", "full_name": "John Doe", "token": "cus_QzJLCrpX49jmmnugVysBDR3T", "is_active": true, "date_modified": "2016-09-30T17:29:52+0300", "cvv_required": false, "card": { "token": "crd_mlyljoZ2nw3SdtPUTh2DGj58", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false }, "cards": { "count": 1, "data": [ { "token": "crd_mlyljoZ2nw3SdtPUTh2DGj58", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false } ] } }, "items": [ { "token": "sci_Wj7t2kj3kCO3uMOBQ1cAqx5x", "schedule_date": "2016-10-07T17:29:51+0300", "amount": 40000, "currency": "eur", "percentage": 20, "payment": null }, { "token": "sci_wQtsyZckvw7e0awSzLgUUg0K", "schedule_date": "2016-10-30T17:29:51+0200", "amount": 60000, "currency": "eur", "percentage": 30, "payment": null }, { "token": "sci_o3ozkMfAJ9eqaf522k0zVBu8", "schedule_date": "2016-11-30T17:29:51+0200", "amount": 100000, "currency": "eur", "percentage": 50, "payment": null } ] }';
    }
}
