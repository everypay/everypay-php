<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class TokenTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
    }

    public function testTokenCreate()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );
        $this->mockResponse($this->success_token_create_response());
        $token = Token::create($params);

        $this->assertObjectHasAttribute('token', $token);
        $this->assertFalse($token->is_used);
        $this->assertFalse($token->has_expired);
    }

    public function testTokenRetrieve()
    {
        $this->mockResponse($this->success_token_create_response());
        $id = 'ctn_oLyYPaymB2AozoABZYYHnb3g';
        $token = Token::retrieve($id);

        $this->assertObjectHasAttribute('token', $token);
        $this->assertFalse($token->is_used);
        $this->assertFalse($token->has_expired);
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Tokens does not support method Everypay\Token::listAll
     */
    public function testTokensListAll()
    {
        $payment = Token::listAll();
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Tokens does not support method Everypay\Token::update
     */
    public function testTokensUpdate()
    {
        $token = 'ctn_oLyYPaymB2AozoABZYYHnb3g';
        $payment = Token::update($token, []);
    }

    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Tokens does not support method Everypay\Token::delete
     */
    public function testTokensDelete()
    {
        $token = 'ctn_oLyYPaymB2AozoABZYYHnb3g';
        $payment = Token::delete($token);
    }

    private function success_token_create_response()
    {
        return '{ "token": "ctn_oLyYPaymB2AozoABZYYHnb3g", "is_used": false, "has_expired": false, "amount": 0, "date_created": "2015-07-08T15:54:50+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" }}';
    }

    private function success_token_retrieve_response()
    {
        return '{ "token": "ctn_oLyYPaymB2AozoABZYYHnb3g", "is_used": false, "has_expired": false, "amount": 0, "date_created": "2015-07-08T15:54:50+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } }';
    }
}
