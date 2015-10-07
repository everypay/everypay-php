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
        AbstractResource::setClientOption(CurlClient::SSL_VERIFY_PEER, 0);
    }

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
    }

    public function testNotificationRetrieve()
    {
        $this->mockResponse($this->success_notification_retrieve_response());
        $notification = PaymentNotification::retrieve('pnt_dRYYu9wuEIH2CGMFEZecfh20');

        $this->assertNotificationProperties($notification);
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
}
