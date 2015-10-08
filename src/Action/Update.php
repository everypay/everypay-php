<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class Update extends AbstractAction
{
    protected $method = ClientInterface::METHOD_PUT;

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return $this->createRequest($this->method);
    }
}
