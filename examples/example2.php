<?php
/**
 * Example 2
 * 
 * Create a card customer and proceed to a payment.
 */

require_once 'bootstrap.php';
require_once 'Everypay/Customers.php';
require_once 'Everypay/Payments.php';

try {
    $params = array(
        'card_number' => '4242424242424242',
        'expiration_month' => '12',
        'expiration_year' => '2015',
        'cvv' => '123',
        'full_name' => 'Company XYZ',
        'email' => 'info@somecompany.com'
    );

    $customer = Everypay_Customers::create($params);

    $payment = Everypay_Payments::create(array(
        'token'       => $customer->token,
        'amount'      => 12000,
        'description' => "Payment for customer " . $customer->token
    ));
    
    echo 'Customer ' . $customer->full_name . ' successfully charged.' . PHP_EOL;
    
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
