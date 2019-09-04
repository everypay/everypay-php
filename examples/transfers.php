<?php

require_once 'bootstrap.php';

use Everypay\Seller;
use Everypay\Payment;
use Everypay\Transfer;
use Everypay\Balance;
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

    $transfer = Transfer::retrieve($transfer->token);

    // update transfer
    $transfer = Transfer::update($transfer->token, array('on_hold' => '0'));
    echo 'Successfully updated on_hold property' . PHP_EOL;

    $params = [
        'type' => 'debit',
        'amount' => '1000',
        'description' => 'Fee for subscription'
    ];
    
    $direct = Transfer::direct($sellerToken, $params);
    echo 'Successfully debited seller account with ' . $direct->amount . ' cents' . PHP_EOL;
    
    // list all transfers
    $transfers = Transfer::listAll();
    echo 'Total count of transfers: ' . $transfers->total_count . PHP_EOL;

    // retrieve seller's balance
    $balance = Seller::balance($sellerToken);
    echo 'Available seller balance: ' . $balance->available_amount . PHP_EOL;

    $marketplaceBalance = Balance::balance();
    echo 'Marketplace available balance: ' . $marketplaceBalance->marketplace->available_amount . PHP_EOL;
    echo 'Marketplace current balance: ' . $marketplaceBalance->marketplace->current_amount . PHP_EOL;

} catch (ApiErrorException $e) {
    echo $e->getMessage() . PHP_EOL;
}
