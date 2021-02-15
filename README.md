# Vodafone MSISDN detect package

## Installation

###### Install package
```
$ composer require arendach/vodafone-msisdn
```

##### Add Service provider

```php
\Arendach\VodafoneMsisdn\Providers\MsisdnServiceProvider::class,
```

###### Publish configs
```
$ php artisan vendor:publish --tag=vodafone-msisdn
```

## How to use

```php

$msisdn = 'xO6843saKpzFW9JF8hMzEA==';
$hmac = 'ihzkBF1kq/g/yCRZ/0mZatgWnrY9LmK3RoGgHk7Hqss=';

// initialize msisdn
$msisdnService = new \Arendach\VodafoneMsisdn\Msisdn();

// get phone number without caching
$phone = $msisdnService->decrypt($msisdn, $hmac);

// decrypt and save to cache + get phone and status
$msisdnService->decryptAndSave($msisdn, $hmac); // method decryptAndSave return decrypted phone or null
$phone = $msisdnService->getPhone();
$status = $msisdnService->getStatus();

// using save phone from other 
$msisdnService->setPhone('380666817731');
$phone = $msisdnService->getPhone();

echo $phone; // 380666817731
echo $status; // -1 | 1

```

## How to use Encryptor

> The Encryptor repeats the encryption functionality for headers on the proxy enrichment
> 
> It can be used for testing

```php
$encryptor = new \Arendach\VodafoneMsisdn\Services\Encrypt();
$phone = '38066681731';
$encrypter->make($phone);

$msisdn = $encrypter->getMsisdn(); // xO6843saKpzFW9JF8hMzEA==
$hmac = $encrypter->getHmac(); // ihzkBF1kq/g/yCRZ/0mZatgWnrY9LmK3RoGgHk7Hqss=
```

## How to configuration .env file

```ini
# HMAC CHECK
MSISDN_HMAC_SECRET='<string: hmac secret key>'
MSISDN_HMAC_ALGO='<string: HMAC algorithm, default: sha256>'
# DECRYPT
MSISDN_DECRYPT_SECRET='<string: msisdn decrypt secret key>'
MSISDN_DECRYPT_IV='<int: msisdn decrypt iv parameter>'
MSISDN_DECRYPT_ALGO='<string: decrypt algorithm, default: aes-256-cbc>'
# Other
MSISDN_DEBUG_MODE='<bool: enable|disable logging and debugging>'
```