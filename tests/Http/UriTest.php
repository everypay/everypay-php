<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http;

class UriTest  extends \PHPUnit_Framework_TestCase
{
    public function testShouldParseUriSimpleSuccessful()
    {
        $case = 'http://www.example.com';
        $uri = new Uri($case);

        $this->assertNull($uri->getPort());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEmpty($uri->getPath());
        $this->assertEmpty($uri->getQuery());
        $this->assertEmpty($uri->getFragment());
        $this->assertEquals($case, $uri->__toString());
    }

    public function testShouldParseUriWithPathSuccessful()
    {
        $case = 'http://www.example.com/payments/1';
        $uri = new Uri($case);

        $this->assertNull($uri->getPort());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('http', $uri->getScheme());
        $this->assertNotEmpty($uri->getPath());
        $this->assertEquals('/payments/1', $uri->getPath());
        $this->assertEmpty($uri->getQuery());
        $this->assertEmpty($uri->getFragment());
        $this->assertEquals($case, $uri->__toString());
    }

    public function testShouldParseUriWithQuerySuccessful()
    {
        $case = 'http://www.example.com/payments/1?foo=bar&baz=bar';
        $uri = new Uri($case);

        $this->assertNull($uri->getPort());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('http', $uri->getScheme());
        $this->assertNotEmpty($uri->getPath());
        $this->assertEquals('/payments/1', $uri->getPath());
        $this->assertNotEmpty($uri->getQuery());
        $this->assertEquals('foo=bar&baz=bar', $uri->getQuery());
        $this->assertEmpty($uri->getFragment());
        $this->assertEquals($case, $uri->__toString());
    }

    public function testShouldParseUriWithFragmentSuccessful()
    {
        $case = 'http://www.example.com/payments/1?foo=bar&baz=bar#order';
        $uri = new Uri($case);

        $this->assertNull($uri->getPort());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('http', $uri->getScheme());
        $this->assertNotEmpty($uri->getPath());
        $this->assertEquals('/payments/1', $uri->getPath());
        $this->assertNotEmpty($uri->getQuery());
        $this->assertEquals('foo=bar&baz=bar', $uri->getQuery());
        $this->assertNotEmpty($uri->getFragment());
        $this->assertEquals('order', $uri->getFragment());
        $this->assertEquals($case, $uri->__toString());
    }

    public function testShouldParseUriWithPortSuccessful()
    {
        $case = 'http://www.example.com:3232/payments/1?foo=bar&baz=bar#order';
        $uri = new Uri($case);

        $this->assertEquals(3232, $uri->getPort());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals('http', $uri->getScheme());
        $this->assertNotEmpty($uri->getPath());
        $this->assertEquals('/payments/1', $uri->getPath());
        $this->assertNotEmpty($uri->getQuery());
        $this->assertEquals('foo=bar&baz=bar', $uri->getQuery());
        $this->assertNotEmpty($uri->getFragment());
        $this->assertEquals('order', $uri->getFragment());
        $this->assertEquals($case, $uri->__toString());
    }
}
