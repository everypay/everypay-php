<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class Update
{
    protected $method = ClientInterface::METHOD_PUT;

    public function __invoke()
    {
        return $this->createRequest($this->method);
    }
}
