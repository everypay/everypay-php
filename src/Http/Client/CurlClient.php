<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Http\Client;

use Everypay\Http\RequestInterface;
use Everypay\Http\Response;
use Everypay\Exception\CurlException;

class CurlClient implements ClientInterface
{
    const UNIX_NEWLINE      = "\n";

    const WINDOWS_NEWLINE   = "\r\n";

    # Expose some common curl options as Client constants.
    const CONNECT_TIMEOUT   = CURLOPT_CONNECTTIMEOUT;
    const TIMEOUT           = CURLOPT_TIMEOUT;
    const SSL_VERIFY_PEER   = CURLOPT_SSL_VERIFYPEER;
    const SSL_VERIFY_HOST   = CURLOPT_SSL_VERIFYHOST;
    const USER_AGENT        = CURLOPT_USERAGENT;

    private $options = array(
        CURLOPT_HEADER          => 1,
        CURLINFO_HEADER_OUT     => 1,
        CURLOPT_RETURNTRANSFER  => 1,
        //close connection when it has finished, not pooled for reuse
        CURLOPT_FORBID_REUSE    => 1,
        // Do not use cached connection
        CURLOPT_FRESH_CONNECT   => 1,
        CURLOPT_CONNECTTIMEOUT  => 5,
        CURLOPT_TIMEOUT         => 7,
    );

    public function __construct(array $options = array())
    {
        $this->options = $this->options + $options;
    }

    public function send(RequestInterface $request)
    {
        $this->resolveUrl($request);
        $this->resolveHeaders($request);
        $this->resolveMethod($request);

        $handler = curl_init();
        if (false === curl_setopt_array($handler, $this->options)) {
            throw new CurlException('Invalid options for cUrl client');
        }

        $result = curl_exec($handler);

        $info = curl_getinfo($handler);

        if (false === $result) {
            $error   = curl_error($handler);
            $errno = curl_errno($handler);
            curl_close($handler);
            throw new CurlException($error, $errno);
        }

        curl_close($handler);

        return $this->resolveResponse($result, $info);
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function getOption($option)
    {
        return array_key_exists($option, $this->options)
            ? $this->options[$option]
            : false;
    }

    public function setOptions(array $options = array())
    {
        $this->options = array_replace($this->options, $options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    protected function resolveResponse($result, $info)
    {
        $statusCode     = $info['http_code'];
        $headersString  = substr($result, 0, $info['header_size']);
        $headers        = $this->resolveResponseHeaders($headersString);
        $body           = substr($result, -$info['size_download']);

        $response = new Response();
        $response = $response->withBody($body)
            ->withStatus($statusCode);
        foreach ($headers as $header => $values) {
            $response = $response->withHeader($header, $values);
        }

        return $response;
    }

    private function resolveResponseHeaders($headers)
    {
        $newLine = strpos($headers, self::UNIX_NEWLINE)
            ? self::UNIX_NEWLINE
            : self::WINDOWS_NEWLINE;

        $headerArray = array();
        $parts = explode($newLine, $headers);
        array_walk($parts, function (&$part) {
            $part = trim(substr($part, 0, -1));
        });
        $headers = array_filter($parts, 'strlen');
        array_shift($headers);
        foreach ($headers as $header) {
            $info = explode(': ', $header, 2);
            $headerArray[$info[0]] = explode(', ', $info[1]);
        }

        return $headerArray;
    }

    private function resolveHeaders(RequestInterface $request)
    {
        $headers = array();

        foreach ($request->getHeaders() as $name => $values) {
            $headers[] = $name . ': ' . implode(", ", $values);
        }

        $this->options[CURLOPT_HTTPHEADER] = $headers;
    }

    private function resolveMethod(RequestInterface $request)
    {
        unset($this->options[CURLOPT_CUSTOMREQUEST]);
        unset($this->options[CURLOPT_POSTFIELDS]);
        unset($this->options[CURLOPT_POST]);
        unset($this->options[CURLOPT_HTTPGET]);

        switch ($request->getMethod()) {
            case static::METHOD_POST:
                $this->options[CURLOPT_POST]       = 1;
                $this->options[CURLOPT_POSTFIELDS] = $request->getBody();
                break;
            case static::METHOD_GET:
                $this->options[CURLOPT_HTTPGET]    = 1;
                break;
            case static::METHOD_PUT:
                $this->options[CURLOPT_POST]          = 1;
                $this->options[CURLOPT_CUSTOMREQUEST] = static::METHOD_PUT;
                $this->options[CURLOPT_POSTFIELDS]    = $request->getBody();
                break;
            case static::METHOD_DELETE:
                $this->options[CURLOPT_CUSTOMREQUEST] = static::METHOD_DELETE;
                break;
        }
    }

    private function resolveUrl(RequestInterface $request)
    {
        $uri = $request->getUri();
        $userInfo = $uri->getUserInfo();

        if (!empty($userInfo)) {
            $this->options[CURLOPT_USERPWD] = $uri->getUserInfo();
        }

        $port = $uri->getPort() ?: 80;

        $port = 'https' == $uri->getScheme() ? 443 : $port;

        $url = $uri->getScheme()
            . '://'
            . $uri->getHost()
            . $uri->getPath()
            . ($uri->getQuery() ? '?' . $uri->getQuery() : null)
            . ($uri->getFragment() ? '#' . $uri->getFragment() : null);

        $this->options[CURLOPT_PORT] = $port;
        $this->options[CURLOPT_URL]  = $url;
    }

    private function resetOptions()
    {
        $this->options = array(

        );
    }
}
