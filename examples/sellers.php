<?php

require_once 'bootstrap.php';

use Everypay\Seller;
use Everypay\Exception\ApiErrorException;

try {
    // create a new seller
    $seller = Seller::create($sellerParams);
    $sellerToken = $seller->token;
    echo 'Seller with token ' . $sellerToken . ' created' . PHP_EOL;
    
    // retrieve seller's details
    $seller = Seller::retrieve($sellerToken);
    $sellerToken = $seller->token;
    
    // update seller's email address
    $seller = Seller::update($sellerToken, ['email' => 'test@example.com']);
    echo 'Successfully updated seller' . PHP_EOL;
    
    // list sellers and return last two
    $sellers = Seller::listAll();
    echo 'Total count of sellers: ' . $sellers->total_count . PHP_EOL;
    
    // retrieve seller's balance
    $balance = Seller::balance($sellerToken);
    echo 'Available seller balance: ' . $balance->available_amount . PHP_EOL;
} catch (ApiErrorException $e) {
    echo $e->getMessage() . PHP_EOL;
}
