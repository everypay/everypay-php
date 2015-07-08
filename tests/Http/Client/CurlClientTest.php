<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http\Client;

use Everypay\Http\Uri;
use Everypay\Http\Request;

class CurlClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = new CurlClient();
    }

    public function testGetRequest()
    {
        $uri = 'http://www.httpbin.org/get';

        $request = $this->createRequest($uri);

        $response = $this->client->send($request);

        $data = $this->unserializeResponse($response);

        $this->assertEquals('www.httpbin.org', $data['headers']['Host']);
    }

    public function testPostRequest()
    {
        $uri = 'http://www.httpbin.org/post';
        $payload = array('foo' => 'bar');

        $stream = $this->createStringFromArray($payload);

        $request = $this->createRequest($uri, ClientInterface::METHOD_POST);
        $request = $request->withBody($stream);

        $data = $this->unserializeResponse($this->client->send($request));

        $this->assertArrayHasKey('foo', $data['form']);
        $this->assertEquals('bar', $data['form']['foo']);
    }

    private function createRequest($uri, $method = ClientInterface::METHOD_GET)
    {
        $request = new Request();

        $request = $request->withUri(new Uri($uri))
            ->withMethod($method);

        return $request;
    }

    private function unserializeResponse($response)
    {
        $string = $response->getBody();

        return json_decode($string, true);
    }

    private function createStringFromArray(array $params)
    {
        return http_build_query($params, null, '&');
    }
}
