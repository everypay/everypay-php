<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Everypay\Everypay;

// Either your live secret API key or your sandbox secret API key.
Everypay::setApiKey('your_secret_api_key');
// set this to true to throw exception if API returns an error response
Everypay::$throwExceptions = true;
// whether to send the requests to sandbox or production environment
Everypay::$isTest = true;

// seller creation params
$sellerParams = array(
    "email" => "tig801972@example.com",
    "contact_phone" => "2204748801",
    "description" => "Seller #75407",
    "business_name" => "Βέργας Ε.Ε",
    "business_title" => "Βέργας Ε.Ε",
    "business_tax_number" => "900206996",
    "business_address" => "Όδος Χαραλαμπίδης, 86-53",
    "bank_account_iban" => "GR308814051PJUD945SDDR60FDV",
    "bank_account_beneficiary" => "κ. Μάρκος Αλεξόπουλος",
    "payout_interval" => "daily",
    "payout_threshold_amount" => "0"
);

$paymentParams = array(
    'card_number'       => '4111111111111111',
    'expiration_month'  => '01',
    'expiration_year'   => '2020',
    'cvv'               => '123',
    'holder_name'       => 'John Doe',
    'split'             => '1',
    'amount'            => 10000 # amount in cents for 10 EURO.
);
