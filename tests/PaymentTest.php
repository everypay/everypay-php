<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class PaymentTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
        Everypay::$isTest = true;
    }

    /**
     *
     * @group   ecommerce
     */
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

        return $payment;
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateCustomer()
    {
        $this->mockResponse($this->success_payment_create_customer_response());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100,
            'create_customer'   => 1
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('customer', $payment);
        $this->assertObjectHasAttribute('card', $payment->customer);

        return $payment->customer;
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateCustomerInvalidParam()
    {
        $this->mockResponse($this->failed_payment_create_response4());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100,
            'create_customer'   => 4
        );
        $response = Payment::create($params);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 40033);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateFromCardToken()
    {
        $this->mockResponse($this->success_payment_create_response());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 900
        );

        if ($this->isRemote()) {
            $token          = Token::create($params);
            $token_string   = $token->token;
        } else {
            $token_string = 'ctn_n8jMLTrjPeRvlHkch0mmWkGU';
        }

        $params2 = array(
            'token'       => $token_string,
        );
        $payment = Payment::create($params2);

        $this->assertPaymentProperties($payment);

        return $token_string;
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateCustomerFromCardToken()
    {
        $this->mockResponse($this->success_payment_create_customer_response2());

        $params = array(
            'card_number'       => '4908440000000003',
            'expiration_month'  => '08',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );

        if ($this->isRemote()) {
            $token          = Token::create($params);
            $token_string   = $token->token;
        } else {
            $token_string = 'ctn_IWCqtxmrTrKgMNS72Wz1aFVy';
        }

        $params2 = array(
            'token'       => $token_string,
            'create_customer' => 1,
            'amount' => 1099
        );
        $payment = Payment::create($params2);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('customer', $payment);
        $this->assertObjectHasAttribute('card', $payment->customer);
        $this->assertEquals($params2['amount'], $payment->amount);

        return $payment->customer;
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateFromCardTokenProvidedDifferentAmount()
    {
        $this->mockResponse($this->failed_payment_create_response2());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 900
        );

        if ($this->isRemote()) {
            $token          = Token::create($params);
            $token_string   = $token->token;
        } else {
            $token_string = 'ctn_foobar';
        }

        $params2 = array(
            'token'       => $token_string,
            'amount'      => 850
        );
        $payment = Payment::create($params2);

        $this->assertObjectHasAttribute('error', $payment);
        $this->assertEquals($payment->error->code, 40029);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateFromCardTokenProvidedNoAmount()
    {
        $this->mockResponse($this->failed_payment_create_response3());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );

        if ($this->isRemote()) {
            $token          = Token::create($params);
            $token_string   = $token->token;
        } else {
            $token_string = 'ctn_foobar';
        }

        $params2 = array(
            'token'       => $token_string
        );
        $payment = Payment::create($params2);

        $this->assertObjectHasAttribute('error', $payment);
        $this->assertEquals($payment->error->code, 40002);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentCreateFromCustomerToken()
    {
        $this->mockResponse($this->success_payment_create_response());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );

        if ($this->isRemote()) {
            $customer       = Customer::create($params);
            $token_string   = $customer->token;
        } else {
            $token_string = 'cus_foobar';
        }

        $params2 = array(
            'token'       => $token_string,
            'amount'      => 650
        );
        $payment = Payment::create($params2);

        $this->assertPaymentProperties($payment);
    }

    /**
     * @depends testPaymentCreateCustomer
     * @group   ecommerce
     */
    public function testPaymentCreateFromCustomerCreatedAutomatically($customer)
    {
        $this->mockResponse($this->success_payment_create_customer_response());

        $params = array(
            'token'       => $customer->token,
            'amount'      => 1099
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('customer', $payment);
        $this->assertObjectHasAttribute('card', $payment->customer);
        $this->assertEquals($customer->token, $payment->customer->token);
        $this->assertEquals($params['amount'], $payment->amount);
    }

    /**
     * @depends testPaymentCreateCustomerFromCardToken
     * @group   ecommerce
     */
    public function testPaymentCreateFromCustomerCreatedAutomatically2($customer)
    {
        $this->mockResponse($this->success_payment_create_customer_response());

        $params = array(
            'token'       => $customer->token,
            'amount'      => 1099
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('customer', $payment);
        $this->assertObjectHasAttribute('card', $payment->customer);
        $this->assertEquals($customer->token, $payment->customer->token);
        $this->assertEquals($params['amount'], $payment->amount);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentInstallments()
    {
        $this->mockResponse($this->success_payment_installments_response());

        $params = array(
            'card_number'       => '4908440000000003',
            'expiration_month'  => '08',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 10480,
            'installments'      => 3
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertEquals($payment->installments_count, 3);
        $this->assertEquals(count($payment->installments), 3);
        $this->assertEquals($params['amount'], $payment->installments[0]->amount
                + $payment->installments[1]->amount
                + $payment->installments[2]->amount);
    }

    /**
     * @depends testPaymentCreateCustomerFromCardToken
     * @group   ecommerce
     */
    public function testPaymentInstallmentsFromCustomerToken($customer)
    {
        $this->mockResponse($this->success_payment_installments_from_customer_token_response());

        $params = array(
            'token'             => $customer->token,
            'amount'            => 1099,
            'installments'      => 2
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertEquals($payment->installments_count, 2);
        $this->assertEquals(count($payment->installments), 2);
        $this->assertEquals($params['amount'], $payment->installments[0]->amount
                + $payment->installments[1]->amount);
    }

    /**
     * @depends testPaymentCreateFromCardToken
     * @group   ecommerce
     */
    //FIXME
    /*public function testPaymentInstallmentsFromCardToken($token_string)
    {
        $this->mockResponse($this->success_payment_installments_response2());

        $params = array(
            'token'             => $token_string,
            'amount'            => 1099,
            'installments'      => 2
        );
        $payment = Payment::create($params);

        $this->assertPaymentProperties($payment);
        $this->assertEquals($payment->installments_count, 2);
        $this->assertEquals(count($payment->installments), 2);
        $this->assertEquals($params['amount'], $payment->installments[0]->amount
                + $payment->installments[1]->amount);
    }*/

    /**
     * This is not allowed from this environment (curl request) for 3D-Secure
     * account.
     *
     * @group   3dsecure
     */
    public function testPaymentCreateNotAllowed()
    {
        $this->mockResponse($this->failed_payment_create_response());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 100
        );
        $payment = Payment::create($params);

        $this->assertObjectHasAttribute('error', $payment);
        $this->assertEquals($payment->error->code, 20011);
    }

    /**
     *
     * @depends testPaymentCreate
     * @group   ecommerce
     */
    public function testPaymentRetrieve($payment_existing)
    {
        $this->mockResponse($this->success_payment_retrieve_response());

        $token  = $payment_existing->token;

        $payment = Payment::retrieve($token);

        $this->assertObjectHasAttribute('token', $payment);
        $this->assertObjectHasAttribute('status', $payment);
        $this->assertObjectHasAttribute('amount', $payment);
        $this->assertObjectHasAttribute('fee_amount', $payment);
    }

    /**
     *
     * @group   ecommerce
     */
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

        return $payment;
    }

    /**
     * @depends testPaymentAuthorize
     * @group   ecommerce
     */
    public function testPaymentCapture($payment_existing)
    {
        $this->mockResponse($this->success_payment_capture_response());

        $token  = $payment_existing->token;

        $payment = Payment::capture($token);

        $this->assertPaymentProperties($payment);
        $this->assertEquals('Captured', $payment->status);

        return $payment;
    }

    /**
     * @depends testPaymentCreate
     * @group   ecommerce
     */
    public function testPaymentVoid($payment_existing)
    {
        $this->mockResponse($this->success_payment_void_response());

        $token   = $payment_existing->token;

        $payment = Payment::refund($token);

        $this->assertPaymentProperties($payment);
        $this->assertObjectHasAttribute('refunds', $payment);
        $this->assertEquals('Refunded', $payment->status);
        $this->assertEquals($payment_existing->amount, $payment->amount);
        $this->assertEquals($payment->refund_amount, $payment->amount);
        $this->assertEquals(0, $payment->fee_amount);
    }

    /**
     * @group   3dsecure
     * @group   ecommerce
     */
    public function testPaymentListAll()
    {

        $this->mockResponse($this->success_payment_list_all_response());

        $collection = Payment::listAll();

        $this->assertObjectHasAttribute('total_count', $collection);
        $this->assertObjectHasAttribute('items', $collection);
    }

    /**
     * @group   3dsecure
     * @group   ecommerce
     */
    public function testPaymentListTooMany()
    {

        $this->mockResponse($this->success_payment_list_toomany_response());

        //able to request a list of up to 20 payments
        $response = Payment::listAll(array('count'=>21));

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 80002);
    }

    /**
     * @depends testPaymentCapture
     * @group   ecommerce
     */
    public function testPaymentRefundNegativeAmount($payment_existing)
    {
        $this->mockResponse($this->failed_payment_refund_response());

        $token = $payment_existing->token;

        $response = Payment::refund($token, array('amount'=>($payment_existing->amount+1)));

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 40007);
    }

    /**
     * @depends testPaymentCapture
     * @group   ecommerce
     */
    public function testPaymentRefund($payment_existing)
    {
        $this->mockResponse($this->success_payment_refund_response());

        $token = $payment_existing->token;

        $payment = Payment::refund($token);

        $this->assertTrue($payment->refunded);
        $this->assertEquals('Refunded', $payment->status);
        $this->assertEquals(0, $payment->fee_amount);

        return $payment;
    }

    /**
     * @depends testPaymentRefund
     * @group   ecommerce
     */
    public function testPaymentRefundAlreadyRefunded($payment_existing)
    {
        $this->mockResponse($this->failed_payment_refund_response2());

        $response = Payment::refund($payment_existing->token);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 40015);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testPaymentPartialRefund()
    {
        $this->mockResponse($this->success_payment_partial_refund_response());

        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'amount'            => 1000
        );
        $payment_existing = Payment::create($params);

        if ($this->isRemote()) {
            $payment = Payment::refund($payment_existing->token, array('amount'=>450));
        } else {
            $payment = $payment_existing; //mock
        }

        $this->assertFalse($payment->refunded);
        $this->assertEquals('Partially Refunded', $payment->status);
        $this->assertNotEquals(0, $payment->fee_amount);
        //$this->assertEquals($payment_existing->amount, $payment->amount);
        $this->assertEquals($payment->refund_amount, 450);
        $this->assertGreaterThan(0, count($payment->refunds));
    }

    /**
     * @group   ecommerce
     */
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

    /**
     * @group   ecommerce
     */
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
        if ($this->isRemote()) {
            $this->markTestSkipped(
                'Test not available in TEST_ENV REMOTE.'
            );
        }
        $this->mockResponse($this->error_payment_response(), 'text/html');
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::refund($token);
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Payments does not support method Everypay\Payment::delete
     * @group                     ecommerce
     * @group                     3dsecure
     */
    public function testPaymentDelete()
    {
        //applicable both in local and remote mode
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::delete($token);
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Payments does not support method Everypay\Payment::update
     * @group                     ecommerce
     * @group                     3dsecure
     */
    public function testPaymentUpdate()
    {
        //applicable both in local and remote mode
        $token = 'pmt_4KQ2DD15gs2w8RS4M2MhBz1Q';
        $payment = Payment::update($token, array());
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
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 0, "installments": [], "installments_count": 0, "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" , "supports_installments": false, "max_installments": 0 } }';
    }

    private function success_payment_create_response()
    {
        return '{ "token": "pmt_guLEyWbxfj9zosdIeyUIWOWP", "date_created": "2015-07-08T18:05:50+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 0, "installments": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": false, "max_installments": 0 } }';
    }

    private function success_payment_create_customer_response()
    {
        return '{ "token": "pmt_8S6CdhqklLIiIjLKRj5k9crb", "date_created": "2015-11-08T18:05:50+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 1099, "refund_amount": 0, "fee_amount": 34, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 0, "installments": [], "customer": { "description": null, "email": null, "date_created": "2015-11-05T12:38:04+0200", "full_name": "John Doe", "token": "cus_qxVtpVXe1VrHdcEzSqaz9KwW", "is_active": true, "date_modified": "2015-11-05T12:38:04+0200", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": false, "max_installments": 0 } } }';
    }

    /*creation of customer (upon succesfull payment) with card that supports installments*/
    private function success_payment_create_customer_response2()
    {
        return '{ "token": "pmt_8S6CdhqklLIiIjLKRj5k9crb", "date_created": "2015-11-08T18:05:50+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 1099, "refund_amount": 0, "fee_amount": 34, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 0, "installments": [], "customer": { "description": null, "email": null, "date_created": "2015-11-05T12:38:04+0200", "full_name": "John Doe", "token": "cus_qxVtpVXe1VrHdcEzSqaz9KwW", "is_active": true, "date_modified": "2015-11-05T12:38:04+0200", "card": { "expiration_month": "08", "expiration_year": "2016", "last_four": "0003", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 3 } } }';
    }

    private function success_payment_installments_response()
    {
        return '{ "token": "pmt_DdEFKTO2lhRZjIpgMcJqj899", "date_created": "2015-10-05T17:37:41+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 10480, "refund_amount": 0, "fee_amount": 312, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 3, "installments": [
            { "token": "pmt_VFDPv5oBSq3ulnlamgKFSSaM", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-10-06T21:00:00+0300", "currency": "EUR", "status": "Pending installment", "amount": 3500, "fee_amount": 104 },
            { "token": "pmt_GMFBFJ2z7EVhtN7HgmbVag6k", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-11-05T21:00:00+0200", "currency": "EUR", "status": "Pending installment", "amount": 3500, "fee_amount": 104 },
            { "token": "pmt_ynvYNpYn5mn9VARuwB4ZbVXY", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-12-07T21:00:00+0200", "currency": "EUR", "status": "Pending installment", "amount": 3480, "fee_amount": 104}
            ], "card": { "expiration_month": "08", "expiration_year": "2016", "last_four": "0003", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 3 } }';
    }

    private function success_payment_installments_response2()
    {
        return '{ "token": "pmt_DdEFKTO2lhRZjIpgMcJqj899", "date_created": "2015-10-05T17:37:41+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 1099, "refund_amount": 0, "fee_amount": 66, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 2, "installments": [
            { "token": "pmt_VFDPv5oBSq3ulnlamgKFSSaM", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-10-06T21:00:00+0300", "currency": "EUR", "status": "Pending installment", "amount": 549, "fee_amount": 33 },
            { "token": "pmt_GMFBFJ2z7EVhtN7HgmbVag6k", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-11-05T21:00:00+0200", "currency": "EUR", "status": "Pending installment", "amount": 550, "fee_amount": 33 },
            ], "card": { "expiration_month": "08", "expiration_year": "2016", "last_four": "0003", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12 } }';
    }

    private function success_payment_installments_from_customer_token_response()
    {
        return '{ "token": "pmt_Xhwgo51I4k9Vee2fLPZhmqi8", "date_created": "2015-10-05T17:37:41+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 1099, "refund_amount": 0, "fee_amount": 66, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "installments_count": 2, "installments": [
            { "token": "pmt_VFDPv5oBSq3ulnlamgKFSSaM", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-10-06T21:00:00+0300", "currency": "EUR", "status": "Pending installment", "amount": 549, "fee_amount": 33 },
            { "token": "pmt_GMFBFJ2z7EVhtN7HgmbVag6k", "date_created": "2015-10-05T17:37:41+0300", "due_date": "2015-11-05T21:00:00+0200", "currency": "EUR", "status": "Pending installment", "amount": 550, "fee_amount": 33 }
            ],
                "customer": { "description": "Order #A-777", "email": null, "date_created": "2015-09-05T14:57:51+0200", "full_name": null, "token": "cus_CHXCbJNsijksZ34D7lFGL5p6", "is_active": true, "date_modified": "2015-09-05T14:58:10+0200",
                    "card": { "expiration_month": "08", "expiration_year": "2016", "last_four": "0003", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12 }
                }
            }';
    }

    private function failed_payment_create_response()
    {
        return '{ "error": { "status": 400, "code": 20011, "message": "3D Secure enabled. Only token payments are allowed"} }';
    }

    private function failed_payment_create_response2()
    {
        return '{ "error": { "status": 400, "code": 40029, "message": "Given amount is not the same that used on token creation."} }';
    }

    private function failed_payment_create_response3()
    {
        return '{ "error": { "status": 400, "code": 40002, "message": "The amount parameter is invalid. Min. 30 cents"} }';
    }

    private function failed_payment_create_response4()
    {
        return '{ "error": { "status": 400, "code": 40033, "message": "Create customer option must be 0 or 1."} }';
    }

    private function success_payment_list_all_response()
    {
        return '{ "total_count": 9, "items": [ { "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_kgq0Ep9dVRjfF7vd4YSDLB9R", "date_created": "2015-07-06T18:03:37+0300", "description": null, "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "test01" } }, { "token": "pmt_tOTC2ljKOJixU3C2UfWYAbLl", "date_created": "2015-07-06T18:00:59+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_XKxHQykCy89mOTdOXTt3QQDs", "date_created": "2015-07-06T17:57:58+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 100, "refund_amount": 0, "fee_amount": 22, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_1eIn8bxXB8PDvjCNIYmaEoBJ", "date_created": "2015-07-06T17:55:38+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 0, "refund_amount": 0, "fee_amount": 20, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_okPh0KmpNurI1HYwFTW7cYtZ", "date_created": "2015-07-06T17:54:26+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 0, "refund_amount": 0, "fee_amount": 20, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_TDMUOtSCuVQxmYFTZQ49LfRl", "date_created": "2015-07-06T17:53:27+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 9945, "refund_amount": 0, "fee_amount": 259, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_1RScvSmzsjH1anAOjPzjk853", "date_created": "2015-07-06T17:43:28+0300", "description": "payment for item #222", "currency": "EUR", "status": "Captured", "amount": 9945, "refund_amount": 0, "fee_amount": 259, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }, { "token": "pmt_8GiEPD5vRGS8DI12jPI8ajvl", "date_created": "2015-07-06T16:01:37+0300", "description": "payment for item #41212121", "currency": "EUR", "status": "Captured", "amount": 50, "refund_amount": 0, "fee_amount": 21, "payee_email": null, "payee_phone": "2106921341", "refunded": false, "refunds": [], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } } ] }';
    }

    private function success_payment_list_toomany_response()
    {
        return '{ "error": { "status": 400, "code": 80002, "message": "The following parameter has an invalid value: count"} }';
    }

    private function success_payment_refund_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1Q", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #222", "currency": "EUR", "status": "Refunded", "amount": 100, "refund_amount": 100, "fee_amount": 0, "payee_email": null, "payee_phone": null, "refunded": true, "refunds": [ { "token": "ref_6KbhjFSSjTx9wYttxzEFHTnL", "status": "Captured", "date_created": "2015-07-08T15:14:29+0300", "amount": 100, "fee_amount": 21, "description": null } ], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function failed_payment_refund_response()
    {
        return '{ "error": { "status": 400, "code": 40007, "message": "Refund amount of 101 exceeds the remaining amount of payment pmt_guLEyWbxfj9zosdIeyUIWOWP"} }';
    }

    private function failed_payment_refund_response2()
    {
        return '{ "error": { "status": 400, "code": 40015, "message": "Payment \"pmt_4KQ2DD15gs2w8RS4M2MhBz1Q\" is marked as \"Refunded\" and cannot be refunded."} }';
    }

    private function success_payment_partial_refund_response()
    {
        return '{ "token": "pmt_4KQ2DD15gs2w8RS4M2MhBz1a", "date_created": "2015-07-06T18:05:01+0300", "description": "payment for item #223", "currency": "EUR", "status": "Partially Refunded", "amount": 1000, "refund_amount": 450, "fee_amount": 33, "payee_email": null, "payee_phone": null, "refunded": false, "refunds": [ { "token": "ref_6KbhjFSSjTx9wYttxzEFHTna", "status": "Captured", "date_created": "2015-07-08T15:14:29+0300", "amount": 450, "fee_amount": 11, "description": null } ], "card": { "expiration_month": "10", "expiration_year": "2017", "last_four": "9610", "type": "Visa", "holder_name": "John Doe" } }';
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
