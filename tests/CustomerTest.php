<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class CustomerTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key_tokenization']);
        Customer::setClientOption(Http\Client\CurlClient::SSL_VERIFY_PEER, 0);
        Everypay::$isTest = true;
    }

    /**
     * 
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

        $this->mockResponse($this->success_customer_create_response());
        $customer = Customer::create($params);

        $this->assertTrue($customer->is_active);
        $this->assertNotNull($customer->token);
        
        return $customer;
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerRetrieve($customer_exists)
    {
        //$token = 'cus_zDdjHBuNW3do8G3jaTqApzsI';
        $this->mockResponse($this->success_customer_retrieve_response());
        $customer = Customer::retrieve($customer_exists->token);

        $this->assertTrue($customer->is_active);
        $this->assertNotNull($customer->token);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerUpdate($customer_exists)
    {
        //$token = 'cus_zDdjHBuNW3do8G3jaTqApzsI';
        $this->mockResponse($this->success_customer_update_response());
        $params = array(
            'email' => 'john_dow@example.com',
            'full_name' => 'John Doe'
        );
        $customer = Customer::update($customer_exists->token, $params);

        $this->assertNotNull($customer->email);
        $this->assertNotNull($customer->full_name);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerUpdateFromCard($customer_exists)
    {
        //$token = 'cus_FaWhNhFT5gEAAv5BArjJSIIq';
        $this->mockResponse($this->success_customer_update_card_response());
        $params = array(
            'card_number'       => '4908440000000003',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );
        $customer = Customer::update($customer_exists->token, $params);

        $this->assertEquals(substr($params['card_number'], -4), $customer->card->last_four);
    }

    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerListAll($customer_exists)
    {
        //$token0 = 'cus_zDdjHBuNW3do8G3jaTqApzsI';
        $this->mockResponse($this->success_customer_listAll_response());
        $customers = Customer::listAll(array('count'=>2));

        $this->assertGreaterThan(0, count($customers->items));
        $this->assertEquals($customers->items[0]->token, $customer_exists->token);
    }
    
    /**
     * @depends testCustomerCreate
     * @group   ecommerce
     */
    public function testCustomerDelete($customer_exists)
    {
        //$token = 'cus_zDdjHBuNW3do8G3jaTqApzsI';
        $this->mockResponse($this->success_customer_delete_response());

        $customer = Customer::delete($customer_exists->token);

        $this->assertNotNull($customer->token);
        $this->assertNotNull($customer->email);
        $this->assertFalse($customer->is_active);
    }

    private function success_customer_create_response()
    {
        return '{ "description": null, "email": null, "date_created": "2015-07-13T12:26:56+0300", "full_name": null, "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": true, "date_modified": "2015-07-13T12:26:56+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" }}';
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

    private function success_customer_update_card_response()
    {
       return '{ "description": null, "email": null, "date_created": "2015-09-18T14:23:51+0300", "full_name": null, "token": "cus_FaWhNhFT5gEAAv5BArjJSIIq", "is_active": true, "date_modified": "2015-09-18T14:26:00+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "0003", "type": "Visa", "holder_name": "John Doe" } }';
    }
}
