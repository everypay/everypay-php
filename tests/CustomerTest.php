<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class CustomerTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
        Customer::setClientOption(Http\Client\CurlClient::SSL_VERIFY_PEER, 0);
        Everypay::$isTest = false;
    }

    /**
     * @group   ecommerce
     */
    public function testCustomerCreate()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );

        #$this->mockResponse($this->successCustomerCreateResponse());
        $customer = Customer::create($params);

        $this->assertTrue($customer->is_active);
        $this->assertNotNull($customer->token);

        return $customer;
    }

    /**
     * @group   3dsecure
     */
    public function testCustomerCreateNotAllowed()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );

        $this->mockResponse($this->failed_customer_create_response());
        $customer = Customer::create($params);

        $this->assertObjectHasAttribute('error', $customer);
        $this->assertEquals($customer->error->code, 20016);
    }

    /**
     * @group ecommerce
     */
    public function testCustomerCreateWithCardToken()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );

        if ($this->isRemote()) {
            $token = Token::create($params);
            $this->assertObjectHasAttribute('token', $token);
            $this->assertFalse($token->is_used);
            $this->assertFalse($token->has_expired);
            $token_string = $token->token;
        } else {
            $token_string = 'some_token';
        }

        $params2 = array(
            'token' => $token_string,
            'email' => 'smith@nowhere.com'
        );
        $this->mockResponse($this->success_customer_create_response2());
        $customer = Customer::create($params2);

        $this->assertObjectHasAttribute('token', $customer, print_r($customer, true));
        $this->assertObjectHasAttribute('card', $customer);
        $this->assertEquals($customer->email, $params2['email']);
    }

    /**
     * @group ecommerce
     */
    public function testCustomerCreateWithoutCardParams()
    {
        $this->mockResponse($this->failed_customer_create_response2());
        $response = Customer::create(array());

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20000);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerRetrieve($customer_existing)
    {
        $this->mockResponse($this->success_customer_retrieve_response());
        $customer = Customer::retrieve($customer_existing->token);

        $this->assertTrue($customer->is_active);
        $this->assertNotNull($customer->token);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerUpdate($customer_existing)
    {
        $this->mockResponse($this->success_customer_update_response());
        $params = array(
            'email' => 'john_dow@example.com',
            'full_name' => 'John Doe'
        );
        $customer = Customer::update($customer_existing->token, $params);

        $this->assertNotNull($customer->email);
        $this->assertNotNull($customer->full_name);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerListAll($customer_existing)
    {
        $this->mockResponse($this->success_customer_listAll_response());
        $customers = Customer::listAll(array('count'=>2));

        $this->assertGreaterThan(0, count($customers->items));
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerDelete($customer_existing)
    {
        $this->mockResponse($this->success_customer_delete_response());

        $customer = Customer::delete($customer_existing->token);

        $this->assertNotNull($customer->token);
        $this->assertNotNull($customer->email);
        $this->assertFalse($customer->is_active);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testAddCustomerCardFromCardToken($customer)
    {
        $customerToken = $customer->token;
        $cardToken = $this->createCardToken();
        $data = array(
            'token' => $cardToken->token,
        );
        $this->mockResponse($this->successCustomerAddCardFromCardTokenResponse());
        $customer = Customer::update($customerToken, $data);

        $this->assertTrue($customer->cards->count > 1);
        $this->assertTrue(isset($customer->cvv_required));

        return $customer;
    }

    /**
     * @depends testAddCustomerCardFromCardToken
     * @group   ecommerce
     */
    public function testSetCustomerDefaultCardFromTokenOfCard($customer)
    {
        $this->mockResponse($this->successSetCustomerDefaultCardFromTokenOfCardResponse());
        $customerToken = $customer->token;

        $noDefaultCard = $this->getNoDefaultCard($customer);
        $this->assertNotFalse($noDefaultCard);

        $data = array(
            'card' => $noDefaultCard->token,
            'default_card' => 1,
        );
        $customer = Customer::update($customerToken, $data);

        $this->assertEquals($data['card'], $customer->card->token);
        $this->assertTrue(isset($customer->cvv_required));
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testAddDefaultCustomerCardFromCardDetails($customer)
    {
        $this->mockResponse($this->successAddDefaultCustomerCardFromCardDetailsResponse());
        $customerToken = $customer->token;
        $params = array(
            'card_number'       => '5309707106499343',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
            'default_card'      => 1
        );

        $customer = Customer::update($customerToken, $params);

        $this->assertEquals(substr($params['card_number'], -4), $customer->card->last_four);
        $this->assertTrue(isset($customer->cvv_required));
    }

    private function createCardToken()
    {
        $this->mockResponse($this->successCreateTokenResponse());

        $params = array(
            'card_number'       => '4908440000000003',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe',
        );

        $response = Token::create($params);

        return $response;
    }

    private function getNoDefaultCard($customer)
    {
        $defaultCard = $customer->card;

        $noDefault = array_filter($customer->cards->data, function ($c) use ($defaultCard) {
            return $c->token !== $defaultCard->token;
        });

        return reset($noDefault);
    }

    private function successCreateTokenResponse()
    {
        return '{ "token": "ctn_QwGjqyMdRnHZgsNpBW0UbR14", "is_used": false, "has_expired": false, "amount": 0, "date_created": "2016-05-24T13:12:58+0300", "card": { "token": "crd_yQt4R8uYSBqErIY02fRLpdpP", "expiration_month": "01", "expiration_year": "2017", "last_four": "0003", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 0003 (01\/2017)", "cvv_required": false } }';
    }

    private function successCustomerCreateResponse()
    {
        return '{ "description": null, "email": null, "date_created": "2016-05-24T13:07:30+0300", "full_name": null, "token": "cus_ezNQMOQnvvj2LnsPLD17WwZr", "is_active": true, "date_modified": "2016-05-24T13:07:30+0300", "cvv_required": false, "card": { "token": "crd_J7j7VMnHegcLQoHacGYm3Uyy", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false }, "cards": { "count": 1, "data": [ { "token": "crd_J7j7VMnHegcLQoHacGYm3Uyy", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false } ] } }';
    }

    private function success_customer_create_response2()
    {
        return '{ "description": null, "email": "smith@nowhere.com", "date_created": "2015-07-13T12:26:56+0300", "full_name": null, "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": true, "date_modified": "2015-07-13T12:26:56+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" }}';
    }

    private function failed_customer_create_response()
    {
        return '{ "error": { "status": 400, "code": 20016, "message": "Your account does not support tokenization."} }';
    }

    private function failed_customer_create_response2()
    {
        return '{ "error": { "status": 400, "code": 20000, "message": "Invalid card number. Please try again or use another card."} }';
    }

    private function success_customer_retrieve_response()
    {
        return '{ "description": null, "email": null, "date_created": "2015-07-13T12:26:56+0300", "full_name": null, "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": true, "date_modified": "2015-07-13T12:26:56+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_customer_update_response()
    {
        return '{ "description": null, "email": "john_dow@example.com", "date_created": "2015-07-13T12:26:56+0300", "full_name": "John Doe", "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": true, "date_modified": "2015-07-13T12:39:37+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function success_customer_delete_response()
    {
        return '{ "description": null, "email": "john_dow@example.com", "date_created": "2015-07-13T12:26:56+0300", "full_name": "John Doe", "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": false, "date_modified": "2015-07-13T12:39:50+0300" }';
    }

    private function success_customer_listAll_response()
    {
        return '{ "total_count":6, "items": [{ "description": null, "email": null, "date_created": "2015-07-13T12:26:56+0300", "full_name": null, "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": true, "date_modified": "2015-07-13T12:26:56+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } },{ "description": "Smith Hill Co", "email": "smith.hill@email.co.uk", "date_created": "2015-07-28T11:19:55+0300", "full_name": null, "token": "cus_b7QO01Ie4csrDAkRjXijK7aM", "is_active": true, "date_modified": "2015-07-28T11:19:55+0300", "card": { "expiration_month": "05", "expiration_year": "2016", "last_four": "4242", "type": "Visa", "holder_name": "Mike Smith" } }] }';
    }

    private function successCustomerAddCardFromCardTokenResponse()
    {
        return '{ "description": "Customer", "email": "john@example.com", "date_created": "2016-05-17T10:05:29+0300", "full_name": "John Dow", "token": "cus_BPlnZQ7Ok5ec5TR7d9DueyYL", "is_active": true, "date_modified": "2016-05-18T16:56:36+0300", "cvv_required": false, "card": { "token": "crd_7j93agQ3zMGlPp83StF9NkxY", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false }, "cards": { "count": 2, "data": [ { "token": "crd_7j93agQ3zMGlPp83StF9NkxY", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false }, { "token": "crd_cuvYshB7mWon47WradKpQoo6", "expiration_month": "01", "expiration_year": "2017", "last_four": "8889", "type": "MasterCard", "holder_name": "TEST", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "MasterCard •••• 8889 (01\/2017)", "cvv_required": false } ] } }';
    }

    private function successSetCustomerDefaultCardFromTokenOfCardResponse()
    {
        return '{ "description": "Customer", "email": "john@example.com", "date_created": "2016-05-17T10:05:29+0300", "full_name": "John Dow", "token": "cus_BPlnZQ7Ok5ec5TR7d9DueyYL", "is_active": true, "date_modified": "2016-05-23T17:32:40+0300", "cvv_required": false, "card": { "token": "crd_cuvYshB7mWon47WradKpQoo6", "expiration_month": "01", "expiration_year": "2017", "last_four": "8889", "type": "MasterCard", "holder_name": "TEST", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "MasterCard •••• 8889 (01\/2017)", "cvv_required": false }, "cards": { "count": 2, "data": [ { "token": "crd_cuvYshB7mWon47WradKpQoo6", "expiration_month": "01", "expiration_year": "2017", "last_four": "8889", "type": "MasterCard", "holder_name": "TEST", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "MasterCard •••• 8889 (01\/2017)", "cvv_required": false }, { "token": "crd_7j93agQ3zMGlPp83StF9NkxY", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false } ] } }';
    }

    private function successAddDefaultCustomerCardFromCardDetailsResponse()
    {
        return '{ "description": null, "email": null, "date_created": "2016-05-24T13:12:58+0300", "full_name": null, "token": "cus_tg75mrhkyb1pa7tvBnVPygg8", "is_active": true, "date_modified": "2016-05-24T13:12:59+0300", "cvv_required": false, "card": { "token": "crd_A0wUY2HO9EinoqD88qqP4Af3", "expiration_month": "01", "expiration_year": "2017", "last_four": "9343", "type": "MasterCard", "holder_name": "John Doe", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "MasterCard •••• 9343 (01\/2017)", "cvv_required": false }, "cards": { "count": 3, "data": [ { "token": "crd_lAkLrZSEdY6hMTHt0GH4oYeR", "expiration_month": "01", "expiration_year": "2017", "last_four": "0003", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 0003 (01\/2017)", "cvv_required": false }, { "token": "crd_6uvEwNPu14QYkZ5SDsIqVQAH", "expiration_month": "01", "expiration_year": "2017", "last_four": "1111", "type": "Visa", "holder_name": "John Doe", "supports_installments": true, "max_installments": 12, "status": "valid", "friendly_name": "Visa •••• 1111 (01\/2017)", "cvv_required": false }, { "token": "crd_A0wUY2HO9EinoqD88qqP4Af3", "expiration_month": "01", "expiration_year": "2017", "last_four": "9343", "type": "MasterCard", "holder_name": "John Doe", "supports_installments": false, "max_installments": 0, "status": "valid", "friendly_name": "MasterCard •••• 9343 (01\/2017)", "cvv_required": false } ] } }';
    }
}
