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

    public function testPaymentCreate()
    {
        $this->mockResponse($this->success_payment_create_response());
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
    }

    public function testPaymentAuthorize()
    {
        $this->mockResponse($this->success_payment_authorize_response());

        $params = array(
            'card_number'       => '4000000000000002',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100,
            'capture'           => 0
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertEquals('Pre authorized', $payment->status);
    }

    public function testPaymentCapture()
    {
        $this->mockResponse($this->success_payment_capture_response());

        $token   = 'pmt_vBbiBMkqyA0YUT5Lz7gS5prY';
        $payment = Payment::capture($token);

        $this->assertPaymentProperties($payment);
        $this->assertEquals('Captured', $payment->status);
    }

    public function testPaymentVoid()
    {
        $this->mockResponse($this->success_payment_void_response());

        $token   = 'pmt_dWOEowLhaNocgsk339P1RYzX';
        $payment = Payment::refund($token);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('refunds', $payment);
        $this->assertEquals('Refunded', $payment->status);
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

    public function testPaymentCreatesCustomer()
    {
        $this->mockResponse($this->success_payment_with_customer_response());
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100,
            'create_customer'   => true
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('customer', $payment);
    }

    public function testAuthorizedPaymentCreatesCustomer()
    {
        $this->mockResponse($this->success_authorize_with_customer_response());
        $params = array(
            'card_number'       => '4000000000000002',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100,
            'create_customer'   => true,
            'capture'           => false
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('customer', $payment);
    }

    /**
     * @expectedException         Everypay\Exception\CurlException
     * @expectedExceptionMessage  The returned response is not in json format
     */
    public function testPaymentError()
    {
        $this->mockResponse($this->error_payment_response(), 'text/html');
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::refund($token);
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Payments does not support method Everypay\Payment::delete
     */
    public function testPaymentDelete()
    {
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::delete($token);
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Payments does not support method Everypay\Payment::update
     */
    public function testPaymentUpdate()
    {
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::update($token, []);
    }

    private function assertPaymentProperties($payment)
    {
        $this->assertObjectHasAttribute('token', $payment);
        $this->assertObjectHasAttribute('status', $payment);
        $this->assertObjectHasAttribute('amount', $payment);
        $this->assertObjectHasAttribute('fee_amount', $payment);
    }

    private function success_payment_retrieve_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_payment_create_response()
    {
        return '{ "token": "pmt_guLEyWbxfj9zosdIeyUIWOWP", "date_created": "2015-07-08T18:05:50+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_payment_list_all_response()
    {
        return '{ "total_count": 9, "items": [ { "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_kgq0Ep9dVRjfF7vd4YSDLB9R", "date_created": "2015-07-06T18:03:37+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "test01" } }, { "token": "pmt_tOTC2ljKOJixU3C2UfWYAbLl", "date_created": "2015-07-06T18:00:59+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_XKxHQykCy89mOTdOXTt3QQDs", "date_created": "2015-07-06T17:57:58+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_1eIn8bxXB8PDvjCNIYmaEoBJ", "date_created": "2015-07-06T17:55:38+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 0, "refund_amount": 0, "fee_amount": 20, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_okPh0KmpNurI1HYwFTW7cYtZ", "date_created": "2015-07-06T17:54:26+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 0, "refund_amount": 0, "fee_amount": 20, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_TDMUOtSCuVQxmYFTZQ49LfRl", "date_created": "2015-07-06T17:53:27+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 9945, "refund_amount": 0, "fee_amount": 259, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_1RScvSmzsjH1anAOjPzjk853", "date_created": "2015-07-06T17:43:28+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 9945, "refund_amount": 0, "fee_amount": 259, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_8GiEPD5vRGS8DI12jPI8ajvl", "date_created": "2015-07-06T16:01:37+0300", "description": "payment for item #41212121", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": "2106921341", "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } } ] }';
    }

    private function success_payment_refund_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Refunded", "amount": 50, "refund_amount": 50, "fee_amount": 0, "payee_email": null, "payee_phone": null, "refunded": true, "refunds": [ { "token": "ref_6KbhjFSSjTx9wYttxzEFHTnL", "status": "Captured", "date_created": "2015-07-08T15:14:29+0300", "amount": 50, "fee_amount": 21, "description": null } ], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_payment_authorize_response()
    {
        return '{ "token": "pmt_vBbiBMkqyA0YUT5Lz7gS5prY", "date_created": "2015-08-17T18:08:01+0300", "description": null, "currency": "EUR", "status": "Pre authorized", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "0002", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_payment_capture_response()
    {
        return '{ "token": "pmt_vBbiBMkqyA0YUT5Lz7gS5prY", "date_created": "2015-08-17T18:08:01+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "0002", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_payment_void_response()
    {
        return '{ "token": "pmt_dWOEowLhaNocgsk339P1RYzX", "date_created": "2015-08-17T18:11:24+0300", "description": null, "currency": "EUR", "status": "Refunded", "amount": 100, "refund_amount": 100, "fee_amount": 0, "payee_email": null, "payee_phone": null, "refunded": true, "refunds": [ { "token": "ref_JMGfCCqJHwEc2wIvG7KuHkMS", "status": "Captured", "date_created": "2015-08-17T18:12:31+0300", "amount": 100, "fee_amount": 22, "description": null } ], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "0002", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function error_payment_response()
    {
        return '{ "error": { "status": 404, "code": 40005, "message": "Could not find payment: pmt_X9QzzUUe9FPakmsCzUX50wul" } }';
    }

    private function success_payment_with_customer_response()
    {
        return '{ "token": "pmt_RyIwmVA2r8T3UMcMIvKcbxGE", "date_created": "2015-08-21T17:57:02+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "customer": { "description": null, "email": null, "date_created": "2015-08-21T17:57:02+0300", "full_name": "John Doe", "token": "cus_Hdv4aPIwIFfRh5Bo609HiaDo", "is_active": true, "date_modified": "2015-08-21T17:57:02+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } } }';
    }

    private function success_authorize_with_customer_response()
    {
        return '{ "token": "pmt_RyIwmVA2r8T3UMcMIvKcbxGE", "date_created": "2015-08-21T17:57:02+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "customer": { "description": null, "email": null, "date_created": "2015-08-21T17:57:02+0300", "full_name": "John Doe", "token": "cus_Hdv4aPIwIFfRh5Bo609HiaDo", "is_active": true, "date_modified": "2015-08-21T17:57:02+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } } }';
    }
}
