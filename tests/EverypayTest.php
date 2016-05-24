<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class EverypayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException         Everypay\Exception\RuntimeException
     * @expectedExceptionMessage  You must set first an API key.
     * @group   ecommerce
     * @group   3dsecure
     */
    public function testThrowExceptionForApiKeys()
    {
        Everypay::reset();

        $key = Everypay::getApiKey();
    }
}
