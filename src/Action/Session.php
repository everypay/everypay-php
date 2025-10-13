<?php

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class Session extends AbstractAction
{
    protected $method = ClientInterface::METHOD_POST;

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return $this->createRequest($this->method);
    }

    protected function getResourceUri()
    {
        return $this->apiUri
            . '/'
            . $this->resource
            . '/sessions';
    }
}
