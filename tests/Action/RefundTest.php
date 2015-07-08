<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\TestCase;

class RefundTest extends TestCase
{
    protected $credentials;

    public function setUp()
    {
        $this->credentials = $this->getFixtures()->offsetGet('everypay');
    }

    public function testShouldCreateRequestProperly()
    {
        $apiUri = 'https://api.everypay.gr';
        $options = array(
            'resource' => 'payments',
            'token_id' => 'pmt_h55mpcAFN26cexat9kAIfmKq',
            'amount'   => 100,
            'api_key'  => $this->credentials['secret_key'],
            'api_uri'  => $apiUri,
        );
        $action = new Refund($options);

        $request = $action->__invoke();

        $this->assertEquals(
            $apiUri . '/payments/refund/' . $options['token_id'],
            $request->getUri()->__toString()
        );

        $this->assertEquals(
            $this->credentials['secret_key'],
            $request->getUri()->getUserInfo()
        );

        $this->assertEquals(
            'amount=100',
            $request->getBody()
        );
    }
}
