<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class CustomerTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key_tokenization']);
    }

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
    }

    private function success_customer_create_response()
    {
        return '{ "description": null, "email": null, "date_created": "2015-07-13T12:26:56+0300", "full_name": null, "token": "cus_zDdjHBuNW3do8G3jaTqApzsI", "is_active": true, "date_modified": "2015-07-13T12:26:56+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" }}';
    }
}
