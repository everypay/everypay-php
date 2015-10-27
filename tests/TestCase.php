<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function getFixtures()
    {
        $ini = parse_ini_file("fixtures.ini", true);
        return new \ArrayIterator($ini);
    }

    protected function mockResponse($answer, $contentType = 'application/json')
    {

        if($this->isRemote()){
            return;
        }
        
        $response = new Http\Response();
        $response = $response->withHeader('Content-Type', $contentType)
            ->withBody($answer);

        $client = $this->getMock(
            'Everypay\\Http\\Client\\CurlClient',
            array('send')
        );

        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        AbstractResource::setClient($client);
    }
    
    protected function isRemote(){
        if(TEST_ENV == 'REMOTE'){
            return 1;
        }else if(TEST_ENV == 'LOCAL'){
            return 0;
        }
    }
}
