<?php

require_once 'bootstrap.php';

use Everypay\Seller;
use Everypay\Payment;
use Everypay\Transfer;
use Everypay\Reversal;
use Everypay\Exception\ApiErrorException;

try {
    // create a new seller
    $seller = Seller::create($sellerParams);
    $sellerToken = $seller->token;
    echo 'Seller with token ' . $sellerToken . ' created' . PHP_EOL;

    // create a new payment with split=1 flag
    $payment = Payment::create($paymentParams);
    echo 'Payment successfully created' . PHP_EOL;

    $params = array(
        'seller' => $sellerToken,
        'amount' => '10000',
        'commission_amount' => '400',
        'description' => 'Order 111',
        'on_hold' => '1'
    );

    // transfer payment amount to seller 
    $transfer = Transfer::payment($payment->token, $params);
    echo 'Successfully transferred ' . $transfer->amount . ' cents to seller' . PHP_EOL;

    // retrieve seller's balance
    $balance = Seller::balance($sellerToken);
    echo 'Available seller balance: ' . $balance->available_amount . PHP_EOL;

    $reversal = Reversal::create(array('transfer' => $transfer->token));
    echo 'Successfully created reversal for transfer ' . $transfer->token . PHP_EOL;

    // retrieve seller's balance
    $balance = Seller::balance($sellerToken);
    echo 'Available seller balance: ' . $balance->available_amount . PHP_EOL;
} catch (ApiErrorException $e) {
    echo $e->getMessage() . PHP_EOL;
}
