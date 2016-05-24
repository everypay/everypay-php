<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Http\Client\ClientInterface;

class Delete extends AbstractAction
{
    protected $method = ClientInterface::METHOD_DELETE;

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

        if (isset($this->params['card'])) {
            return $uri
                . '/card/'
                . $this->params['card'];
        }

        return $uri;
    }
}
