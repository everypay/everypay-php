<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay\Action;

use Everypay\Exception\InvalidArgumentException;
use Everypay\Http\Request;
use Everypay\Http\Uri;
use Everypay\Http\Client\ClientInterface;

abstract class AbstractAction
{
    protected $tokenId;

    protected $params;

    protected $resource;

    protected $apiKey;

    protected $apiUri;

    public function __construct(array $options = array())
    {
        $this->resolveOptions($options);
    }

    /**
     * Executes current action class.
     *
     * @return \Everypay\Http\RequestInterface
     */
    abstract public function __invoke();

    protected function getResourceUri()
    {
        return $this->apiUri
            . '/'
            . $this->resource
            . ($this->tokenId ? '/' . $this->tokenId : null);
    }

    /**
     * @param string $method REquest method.
     *
     * @return \Everypay\Http\RequestInterface
     */
    protected function createRequest($method)
    {
        $request = new Request();
        $uri = new Uri($this->getResourceUri());
        $uri = $uri->withUserInfo($this->apiKey);

        $request = $request->withMethod($method)
            ->withUri($uri);

        if (in_array($method, array(ClientInterface::METHOD_POST, ClientInterface::METHOD_PUT))) {
            $request = $request->withBody($this->createStringFromArray($this->params));
        }

        return $request;
    }

    private function createStringFromArray(array $params)
    {
        return http_build_query($params, null, '&');
    }

    private function resolveOptions($options)
    {
        if (!array_key_exists('resource', $options)) {
            throw new InvalidArgumentException('Resource name must be present.');
        }
        $this->resource = $options['resource'];
        unset($options['resource']);

        $this->apiKey = $options['api_key'];
        unset($options['api_key']);

        $this->apiUri = $options['api_uri'];
        unset($options['api_uri']);

        $this->tokenId = array_key_exists('token_id', $options)
            ? $options['token_id'] : null;
        unset($options['token_id']);

        $this->params = $options;
    }
}
