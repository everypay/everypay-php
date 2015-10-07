# Everypay php library
[![Build Status](https://travis-ci.org/everypay/everypay-php.svg)](https://travis-ci.org/everypay/everypay-php) [![Coverage Status](https://coveralls.io/repos/everypay/everypay-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/everypay/everypay-php?branch=master) [![Latest Stable Version](https://poser.pugx.org/everypay/everypay-php/v/stable)](https://packagist.org/packages/everypay/everypay-php) [![License](https://poser.pugx.org/everypay/everypay-php/license)](https://packagist.org/packages/everypay/everypay-php)
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

use Everypay;
use Everypay\Payment;

Everypay::setApiKey('sk_YoUraPikEy');

$params = array(
    'card_number'       => '4111111111111111',
    'expiration_month'  => '01',
    'expiration_year'   => '2020',
    'cvv'               => '123',
	'holder_name'       => 'John Doe'
);

Payment::create($params);

```

## Documentation

Please see https://www.everypay.gr/api/ for up to date documentation.