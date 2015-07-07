<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http;

class Request extends Message implements RequestInterface
{
    private $method = '';

    private $uri;

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri)
    {
        $new = clone $this;
        $new->uri = $uri;
        return $new;
    }
}
