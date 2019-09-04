<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class Direct extends AbstractAction
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
        return $this->apiUri . '/sellers'
            . ($this->tokenId ? '/' . $this->tokenId : null)
            . '/transfers';
    }
}
