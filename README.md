# Everypay php library
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/everypay/everypay-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/everypay/everypay-php/?branch=master) [![Build Status](https://travis-ci.org/everypay/everypay-php.svg)](https://travis-ci.org/everypay/everypay-php?branch=master) [![Coverage Status](https://coveralls.io/repos/everypay/everypay-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/everypay/everypay-php?branch=master) [![Latest Stable Version](https://poser.pugx.org/everypay/everypay-php/v/stable)](https://packagist.org/packages/everypay/everypay-php) [![License](https://poser.pugx.org/everypay/everypay-php/license)](https://packagist.org/packages/everypay/everypay-php)
## Installation

Include `autoload.php` file in your application.
```php
<?php
require_once '/path/to/everypay-php/autoload.php';
```

## Installation with composer
You can install this library using [Composer](http://getcomposer.org)

Information about how to install composer you can find [here](https://getcomposer.org/doc/00-intro.md) 

### Command line
In root directory of your project run through a console:
```bash
$ composer require "everypay/everypay-php":"@stable"
```
### Composer.json
Include require line in your ```composer.json``` file
```json
{
	"require": {
    	"everypay/everypay-php": "@stable"
    }
}
```
and run from console in the root directory of your project:
```bash
$ composer update
```

After this you must require autoload file from composer.
```php
<?php
require_once 'vendor/autoload.php';
```

## Getting Started

```php
<?php

use Everypay\Everypay;
use Everypay\Payment;

/**
 * Either your live secret API key or your sandbox secret API key.
 */
Everypay::setApiKey('sk_YoUraPikEy');

/** 
 * Set this true to test your sandbox account (also provide your sandbox secret API key above).
 * Ommit it or set it false to actually use your live account (also provide your live secret API key above 
 * - but be carefull, this is no longer a test!).
 */
Everypay::$isTest = true;

$params = array(
    'card_number'       => '4111111111111111',
    'expiration_month'  => '01',
    'expiration_year'   => '2020',
    'cvv'               => '123',
	'holder_name'       => 'John Doe',
    'amount'            => 1000 # amount in cents for 10 EURO.
);

Payment::create($params);

```

## Documentation

Please see https://www.everypay.gr/api/ for up to date documentation.

## Testing

First fill in your API keys in file fixtures.ini (please provide your sandbox-account API key).

Then, in root folder run one of the following available commands.

<ul>
<li>command-1 makes real API requests and applies to a 3D Secure account type.
</li>
<li>command-2 makes real API requests and applies to an eCommerce account type.
</li>
<li>command-3 just performs local tests that do not make any real calls anywhere.
</li>
</ul>

```php
<?php

//command-1: testing with real requests to API 3D-Secure account
phpunit --configuration ./phpunit_remote.xml --group 3dsecure

//command-2: testing with real requests to API eCommerce account
phpunit --configuration ./phpunit_remote.xml --group ecommerce

//command-3: testing locally with mocks (default)
phpunit --configuration ./phpunit_local.xml
```

You may provide a specific test file at the end of each command (eg tests/PaymentTests.php) or else all tests will be performed (default).

<b>Note 1:</b> if you try to run one of the "live" API commands (1 or 2) that does not respond to your exact account type (3D-Secure or eCommerce) then that tests may fail or be skipped.

<b>Note 2:</b> if you do not have phpunit installed in your system, you may use composer to install it (provided you have already installed composer itself).
```shell
//in everypay-php root folder
composer update
```

<b>Note 3:</b> as regards the "live" API requests (commands 1 and 2) make sure that in every test file, inside <b>public function setUp</b>, you safely provide the following command (already provided  by default) in order for the calls to be redirected to your appropriate sandbox account rather than the real account.

```php
<?php
Everypay::$isTest = true;
```

<b>Attention:</b> not providing this command <b>along with</b> filling a real API key rather than  the sandbox API key, will make your remote test calls (see commands 1 and 2 above) to be directed to your real account and therefore may result in real charges!