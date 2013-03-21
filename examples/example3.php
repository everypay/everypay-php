<?php
/**
 * Example 3
 * 
 * 1. Create a card token
 * 2. Create a customer with this token
 * 3. Charge the customer
 * 4. Refund the payment
 */

require_once 'bootstrap.php';
require_once 'Everypay/Tokens.php';
require_once 'Everypay/Customers.php';
require_once 'Everypay/Payments.php';

try {
    $params = array(
        'card_number' => '4242424242424242',
        'expiration_month' => '12',
        'expiration_year' => '2015',
        'cvv' => '123'
    );
    
    $token = Everypay_Tokens::create($params);
    echo 'Card token created' . PHP_EOL;
    
    $customer = Everypay_Customers::create(array(
        'token' => $token->token
    ));
    
    echo 'Customer created' . PHP_EOL;
    
    $payment = Everypay_Payments::create(array(
        'token'       => $customer->token,
        'amount'      => 12000
    ));
    
    echo 'Payment created' . PHP_EOL;
    
    // Full refund of payment
    $payment = Everypay_Payments::refund($payment);
    
    echo 'Payment fully refunded' . PHP_EOL;
    
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
