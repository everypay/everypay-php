<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

use Everypay\Http\Client\CurlClient;

class PaymentNotificationTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
        Everypay::$isTest = true;
        AbstractResource::setClientOption(CurlClient::SSL_VERIFY_PEER, 0);
    }

    /**
     *
     * @group   ecommerce
     * @group   3dsecure
     */
    public function testNotificationCreate()
    {
        $this->mockResponse($this->success_notification_create_response());

        $params = array(
            'amount'            => 100,
            'payee_name'        => 'John Doe',
            'payee_email'       => 'john.doe@everypay.gr',
            'payee_phone'       => '6945777777',
            'description'       => 'Test payment notification.'
        );

        $notification = PaymentNotification::create($params);
        $this->assertNotificationProperties($notification);

        return $notification;
    }

    /**
     * @depends testNotificationCreate
     * @group   ecommerce
     * @group   3dsecure
     */
    public function testNotificationRetrieve($notification_existing)
    {
        $this->mockResponse($this->success_notification_retrieve_response());

        $token  = $notification_existing->token;

        $notification = PaymentNotification::retrieve($token);

        $this->assertNotificationProperties($notification);
    }

    /**
     * @depends testNotificationCreate
     * @group   ecommerce
     * @group   3dsecure
     */
    public function testNotificationListAll($notification_existing)
    {
        $this->mockResponse($this->success_notification_listAll_response());
        $notifications = PaymentNotification::listAll(array('count'=>2));

        $this->assertGreaterThan(0, count($notifications->items));
    }

    private function assertNotificationProperties($notification)
    {
        $this->assertObjectHasAttribute('token', $notification);
        $this->assertObjectHasAttribute('status', $notification);
        $this->assertObjectHasAttribute('amount', $notification);
    }

    private function success_notification_create_response()
    {
        return ' { "token": "pnt_dRYYu9wuEIH2CGMFEZecfh20", "status": "Awaiting", "date_created": "2015-10-07T13:09:25+0300", "description": "Test payment notification.", "amount": 100, "payee_email": "john.doe@everypay.gr", "payee_phone": "6945777777", "expiration_date": null, "locale": "el" } ';
    }

    private function success_notification_retrieve_response()
    {
        return ' { "token": "pnt_dRYYu9wuEIH2CGMFEZecfh20", "status": "Awaiting", "date_created": "2015-10-07T13:09:25+0300", "description": "Test payment notification.", "amount": 100, "payee_email": "john.doe@everypay.gr", "payee_phone": "6945777777", "expiration_date": null, "locale": "el" } ';
    }

    private function success_notification_listAll_response()
    {
        return '{
    "total_count": 10,
    "items": [
        {
            "token": "pnt_ZO0maRe68evdrVnLDmdsR3xE",
            "status": "Awaiting",
            "date_created": "2016-01-14T13:21:29+0200",
            "description": "Δόση 1η (2016)",
            "amount": 4500,
            "payee_name": "John Doe",
            "payee_email": "john.doe@gmail.com",
            "payee_phone": "2106561444",
            "expiration_date": "2016-02-25T23:59:00+0200",
            "locale": "el"
        },
        {
            "token": "pnt_RobdUp58eeNzUQtOhaFHwxhi",
            "status": "Expired",
            "date_created": "2015-10-12T16:31:03+0300",
            "description": "Καλάθι #2199",
            "amount": 1100,
            "payee_name": "John Doe",
            "payee_email": "john.doe@gmail.com",
            "payee_phone": "2106561444",
            "expiration_date": "2015-10-22T23:59:00+0300",
            "locale": "el"
        },
        {
            "token": "pnt_UuvUZgi12l76CuqBdRv1XLbK",
            "status": "Expired",
            "date_created": "2015-10-12T16:25:10+0300",
            "description": "Δόση 12",
            "amount": 2500,
            "payee_name": "Mike Doe",
            "payee_email": "mike.doe@gmail.com",
            "payee_phone": "2106561333",
            "expiration_date": "2015-10-15T23:59:00+0300",
            "locale": "el"
        }
    ]
}';
    }
}
