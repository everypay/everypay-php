<?php

set_include_path(implode(PATH_SEPARATOR, array(
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src',
    get_include_path()
)));

require_once 'Everypay.php';

/**
 * Don't forget this!
 */
$key = 'sk_SJVEJD6ciomRUYu7zVpkKjM6gPgbFIKs';
EveryPay::setApiKey($key);

/**
 * Throw exceptions if API returned an HTTP error code
 * Otherwise the error is returned as an stdClass object.
 * 
 * Default is set to true
 */
EveryPay::throwExceptions(true);

/**
 * Optional check to determine if PHP enviroment
 * does have the needed extensions available, such as json and curl.
 */
try {
    EveryPay::checkRequirements();
} catch (RuntimeException $e) {
    // extension not found..
}
