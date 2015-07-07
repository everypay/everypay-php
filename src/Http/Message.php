<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http;

abstract class Message
{
    protected $headers = array();

    private $headerNames = array();

    private $protocol = '1.1';

    private $body;

    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return array_key_exists(strtolower($name), $this->headerNames);
    }

    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return array();
        }

        $header = $this->headerNames[strtolower($name)];
        $value = $this->headers[$header];
        return is_array($value) ? $value : array($value);
    }

    public function getHeaderLine($name)
    {
        $value = $this->getHeader($name);
        if (empty($value)) {
            return '';
        }
        return implode(',', $value);
    }

    public function withHeader($name, $value)
    {
        if (is_string($value)) {
            $value = array($value);
        }

        $normalized = strtolower($name);

        $new = clone $this;
        $new->headerNames[$normalized] = $name;
        $new->headers[$name] = $value;
        return $new;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody($body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
}
