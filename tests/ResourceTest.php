<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class ResourceTest extends TestCase
{
    /**
     * @expectedException         Everypay\Exception\InvalidArgumentException
     * @expectedExceptionMessage  Resource `custom` does not exists
     *
     * @return void
     */
    public function testCustomResourceCall()
    {
        CustomResource::create(array());
    }

    /**
     * @expectedException         Everypay\Exception\InvalidArgumentException
     * @expectedExceptionMessage  Action `purge` does not exists
     *
     * @return void
     */
    public function testCustomResourceAction()
    {
        CustomResource::purge();
    }
}
