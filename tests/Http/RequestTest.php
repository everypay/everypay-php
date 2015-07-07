<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $request = new Request();
        $uri = new Uri('http://www.example.com');

        $r = $request->withMethod('POST')
            ->withUri($uri);

        $this->assertInstanceOf('Everypay\\Http\\Request', $r);
    }
}
