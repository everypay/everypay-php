<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class ListAll extends AbstractAction
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

        if (!empty($this->params)) {
            $uri .= '?' . http_build_query($this->params, '', '&');
        }

        return $uri;
    }
}
