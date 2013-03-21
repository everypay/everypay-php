<?php
/**
 * Example 4
 * 
 * 1. Retrieve an existing customer
 * 2. Update his email
 */

require_once 'bootstrap.php';
require_once 'Everypay/Customers.php';

try {
    $token = 'cus_wRL7mxEQoBBSX02CA9neWUNC';
    
    $customer = Everypay_Customers::retrieve($token);
    
    $customer = Everypay_Customers::update(
        $customer, array('email' => 'newmail@somecompany.com')
    );
    
    echo 'Customer successfully updated' . PHP_EOL;
    
} catch (Everypay_Exception_ApiErrorException $e) {
    echo 'API Error: ' . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
