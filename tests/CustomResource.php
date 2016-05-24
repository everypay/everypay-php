<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Everypay;

class CustomResource extends AbstractResource
{
    const RESOURCE_NAME = 'custom';

    public static function create(array $params)
    {
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME, $params);
    }

    public static function purge()
    {
        return self::invoke(__FUNCTION__, static::RESOURCE_NAME);
    }
}
