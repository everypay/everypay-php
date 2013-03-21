<?php
/**
 * Example 1
 * 
 * Create a card token and proceed to a payment.
 * 
 */

require_once 'bootstrap.php';
require_once 'Everypay/Tokens.php';
require_once 'Everypay/Payments.php';

try {
    $params = array(
        'card_number' => '4242424242424242',
        'expiration_month' => '12',
        'expiration_year' => '2015',
        'cvv' => '123'
    );

    $token = Everypay_Tokens::create($params);

    $payment = Everypay_Payments::create(array(
        'token'       => $token->token,
        'amount'      => 10010,
        //'description' => "Payment description"
    ));
    
    echo 'Payment successfully created ' . $payment->token . PHP_EOL;
    
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
