<?php

require_once 'bootstrap.php';

use Everypay\Iris;
use Everypay\Exception\ApiErrorException;

try {
    $sessionParams = array(
        'amount' => 100,
        'currency' => 'EUR',
        'country' => 'GR',
        'callback_url' => 'https://your-callback_url',
        'uuid' => '975b48f9-06b0-41f6-98ad-87eb51d7103e',
        'md' => 'test-md-data',
        'webhook_url' => 'https://webhook-url'
    );

    // create a new iris session
    $irisSession = Iris::session($sessionParams);
    $irisSessionToken = $irisSession->token;

    echo 'Iris session with token ' . $irisSessionToken . ' created' . PHP_EOL;
} catch (ApiErrorException $e) {
    echo $e->getMessage() . PHP_EOL;
}
