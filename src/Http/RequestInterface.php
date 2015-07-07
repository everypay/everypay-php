<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http;

interface RequestInterface
{
    public function getMethod();

    public function withMethod($method);

    public function getUri();

    public function withUri(UriInterface $uri);

    public function getProtocolVersion();

    public function withProtocolVersion($version);

    public function getHeaders();

    public function hasHeader($name);

    public function getHeader($name);

    public function getHeaderLine($name);

    public function withHeader($name, $value);

    public function getBody();

    public function withBody($body);

}
