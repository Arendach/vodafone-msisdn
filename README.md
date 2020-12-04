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