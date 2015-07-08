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

    public function testPaymentListAll()
    {
        $this->mockResponse($this->success_payment_list_all_response());
        $collection = Payment::listAll();

        $this->assertObjectHasAttribute('total_count', $collection);
        $this->assertObjectHasAttribute('items', $collection);
        $this->assertEquals(count($collection->items), $collection->total_count);
    }

    public function testPaymentRefund()
    {
        $this->mockResponse($this->success_payment_refund_response());
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::refund($token);

        $this->assertTrue($payment->refunded);
        $this->assertEquals('Refunded', $payment->status);
        $this->assertEquals(0, $payment->fee_amount);
    }

    private function success_payment_retrieve_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_payment_list_all_response()
    {
        return '{ "total_count": 9, "items": [ { "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_kgq0Ep9dVRjfF7vd4YSDLB9R", "date_created": "2015-07-06T18:03:37+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "test01" } }, { "token": "pmt_tOTC2ljKOJixU3C2UfWYAbLl", "date_created": "2015-07-06T18:00:59+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_XKxHQykCy89mOTdOXTt3QQDs", "date_created": "2015-07-06T17:57:58+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_1eIn8bxXB8PDvjCNIYmaEoBJ", "date_created": "2015-07-06T17:55:38+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 0, "refund_amount": 0, "fee_amount": 20, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_okPh0KmpNurI1HYwFTW7cYtZ", "date_created": "2015-07-06T17:54:26+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 0, "refund_amount": 0, "fee_amount": 20, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_TDMUOtSCuVQxmYFTZQ49LfRl", "date_created": "2015-07-06T17:53:27+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 9945, "refund_amount": 0, "fee_amount": 259, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_1RScvSmzsjH1anAOjPzjk853", "date_created": "2015-07-06T17:43:28+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 9945, "refund_amount": 0, "fee_amount": 259, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_8GiEPD5vRGS8DI12jPI8ajvl", "date_created": "2015-07-06T16:01:37+0300", "description": "payment for item #41212121", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": "2106921341", "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } } ] }';
    }

    private function success_payment_refund_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Refunded", "amount": 50, "refund_amount": 50, "fee_amount": 0, "payee_email": null, "payee_phone": null, "refunded": true, "refunds": [ { "token": "ref_6KbhjFSSjTx9wYttxzEFHTnL", "status": "Captured", "date_created": "2015-07-08T15:14:29+0300", "amount": 50, "fee_amount": 21, "description": null } ], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
    }
}
