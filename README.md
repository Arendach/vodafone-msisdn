# Vodafone MSISDN detect package

## Installation

###### Install package
```
$ composer require arendach/vodafone-msidn
```
###### Publish configs
```
$ php artisan vendor:publish --tag=vodafone-msisdn
```

## Logging

> If you need to save logs, then add the «msisdn» channel to the config/logging.php file

```php

...

'msisdn' => [
    'driver' => 'daily',
    'path'   => storage_path('logs/msisdn.log'),
    'level'  => 'debug',
],

...

```
> and set var in to .env file

```ini
MSISDN_DEBUG_MODE=true
```

## How to use

```php

$msisdn = 'xO6843saKpzFW9JF8hMzEA==';
$hmacHash = 'ihzkBF1kq/g/yCRZ/0mZatgWnrY9LmK3RoGgHk7Hqss=';

// initialize msisdn
$msisdnService = new Vodafone\Msisdn\Msisdn();

$phone = $msisdnService->get($msisdn, $hmacHash);

echo $phone; // 380666817731

```