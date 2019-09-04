<?php

require_once 'bootstrap.php';

use Everypay\Seller;
use Everypay\Payout;
use Everypay\Exception\ApiErrorException;

try {
    // create a new seller
    $seller = Seller::create($sellerParams);
    $sellerToken = $seller->token;
    echo 'Seller with token ' . $sellerToken . ' created' . PHP_EOL;

    $payouts = Payout::listAll(['seller' => $sellerToken]);
    echo 'Total count of payouts: ' . $payouts->total_count . PHP_EOL;

    //$payout = Payout::retrieve('pay_J4cjJqNeUBje5c6dmlFmDwXA');
} catch (ApiErrorException $e) {
    echo $e->getMessage() . PHP_EOL;
}
