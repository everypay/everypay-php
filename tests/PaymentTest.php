<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class PaymentTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
    }

    public function testPaymentRetrieve()
    {
        $this->mockResponse($this->success_payment_retrieve_response());
        $payment = Payment::retrieve('pmt_4KQ2DD15gs2w8RS4M2MhBz1Q');

        $this->assertObjectHasAttribute('token', $payment);
        $this->assertObjectHasAttribute('status', $payment);
        $this->assertObjectHasAttribute('amount', $payment);
        $this->assertObjectHasAttribute('fee_amount', $payment);
    }

    private function success_payment_retrieve_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
    }
}
