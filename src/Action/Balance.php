<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class Balance extends AbstractAction
{
    protected $method = ClientInterface::METHOD_GET;

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return $this->createRequest($this->method);
    }

    protected function getResourceUri()
    {
        $uri = parent::getResourceUri();

        if ($this->resource === 'balance') {
            return $uri;
        }

        return $uri . '/balance/';
    }
}
