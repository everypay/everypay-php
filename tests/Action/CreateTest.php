<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\TestCase;

class CreateTest extends TestCase
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
            'token'    => 'ctn_h55mpcAFN26cexat9kAIfmKq',
            'amount'   => 100,
            'api_key'  => $this->credentials['secret_key'],
            'api_uri'  => $apiUri,
        );
        $action = new Create($options);

        $request = $action->__invoke();

        $this->assertEquals(
            $apiUri . '/payments',
            $request->getUri()->__toString()
        );

        $this->assertEquals(
            $this->credentials['secret_key'],
            $request->getUri()->getUserInfo()
        );

        $this->assertEquals(
            'token=ctn_h55mpcAFN26cexat9kAIfmKq&amount=100',
            $request->getBody()
        );
    }
}
