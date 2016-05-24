<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class TokenTest extends TestCase
{
    public function setUp()
    {
        $credentials = $this->getFixtures()->offsetGet('everypay');
        Everypay::setApiKey($credentials['secret_key']);
        Everypay::$isTest = true;
    }

    /**
     * @group   ecommerce
     */
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

        return $token;
    }

    /**
     * This is not generally forbidden for 3D-Secure account,
     * just not allowed from this environment (curl request) for 3D-Secure account.
     *
     * @group   3dsecure
     */
    public function testTokenCreateNotAllowed()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );
        $this->mockResponse($this->failed_token_create_response());
        $response = Token::create($params);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20013);
    }

    /**
     *
     * @depends testTokenCreate
     * @group   ecommerce
     */
    public function testTokenRetrieve($token_existing)
    {
        $this->mockResponse($this->success_token_create_response());
        //$id = 'ctn_oLyYPaymB2AozoABZYYHnb3g';
        $token = Token::retrieve($token_existing->token);

        $this->assertObjectHasAttribute('token', $token);
        $this->assertFalse($token->is_used);
        $this->assertFalse($token->has_expired);
    }

    /**
     * @group   ecommerce
     * @group   3dsecure
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Tokens does not support method Everypay\Token::listAll
     */
    public function testTokensListAll()
    {
        //applicable both in local and remote mode
        $payment = Token::listAll();
    }

    /**
     * @group   ecommerce
     * @group   3dsecure
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Tokens does not support method Everypay\Token::update
     */
    public function testTokensUpdate()
    {
        //applicable both in local and remote mode
        $token = 'ctn_oLyYPaymB2AozoABZYYHnb3g';
        $payment = Token::update($token, array());
    }

    /**
     * @group   ecommerce
     * @group   3dsecure
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  Resource Tokens does not support method Everypay\Token::delete
     */
    public function testTokensDelete()
    {
        //applicable both in local and remote mode
        $token = 'ctn_oLyYPaymB2AozoABZYYHnb3g';
        $payment = Token::delete($token);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testTokenCreateWithInvalidExpirationYear()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => '00',
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );
        $this->mockResponse($this->failed_token_create_response_exp_year());
        $response = Token::create($params);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20001);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testTokenCreateWithExpiredCard()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') - 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );
        $this->mockResponse($this->failed_token_create_response_exp_year());
        $response = Token::create($params);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20001);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testTokenCreateWithInvalidExpirationMonth()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '14',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '123',
            'holder_name'       => 'John Doe'
        );
        $this->mockResponse($this->failed_token_create_response_exp_month());
        $response = Token::create($params);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20002);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testTokenCreateWithInvalidCvv()
    {
        $params = array(
            'card_number'       => '4111111111111111',
            'expiration_month'  => '01',
            'expiration_year'   => date('Y') + 1,
            'cvv'               => '11',
            'holder_name'       => 'John Doe'
        );
        $this->mockResponse($this->failed_token_create_response_invalid_cvv());
        $response = Token::create($params);

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20003);
    }

    /**
     *
     * @group   ecommerce
     */
    public function testTokenRetrieveInvalidToken()
    {
        $this->mockResponse($this->failed_token_create_response_invalid_token());
        $response = Token::retrieve('ctn_oLyYPaymB2AozoABZYYH');

        $this->assertObjectHasAttribute('error', $response);
        $this->assertEquals($response->error->code, 20005);
    }

    private function success_token_create_response()
    {
        return '{ "token": "ctn_oLyYPaymB2AozoABZYYHnb3g", "is_used": false, "has_expired": false, "amount": 0, "date_created": "2015-07-08T15:54:50+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" }}';
    }

    private function success_token_retrieve_response()
    {
        return '{ "token": "ctn_oLyYPaymB2AozoABZYYHnb3g", "is_used": false, "has_expired": false, "amount": 0, "date_created": "2015-07-08T15:54:50+0300", "card": { "expiration_month": "01", "expiration_year": "2016", "last_four": "1111", "type": "Visa", "holder_name": "John Doe" } }';
    }

    private function failed_token_create_response_exp_year()
    {
        return '{ "error": { "status": 400, "code": 20001, "message": "Expiration year in the past or invalid."} }';
    }

    private function failed_token_create_response_exp_month()
    {
        return '{ "error": { "status": 400, "code": 20002, "message": "Expiration month in the past or invalid."} }';
    }

    private function failed_token_create_response_invalid_token()
    {
        return '{ "error": { "status": 400, "code": 20005, "message": "Could not find requested card token."} }';
    }

    private function failed_token_create_response_invalid_cvv()
    {
        return '{ "error": { "status": 400, "code": 20003, "message": "Provide a valid (3 digit) CVV code."} }';
    }

    private function failed_token_create_response()
    {
        return '{ "error": { "status": 400, "code": 20013, "message": "Your account does not support tokenization."} }';
    }
}
