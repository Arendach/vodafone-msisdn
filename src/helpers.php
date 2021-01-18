<?php

declare(strict_types=1);

use Arendach\VodafoneMsisdn\Msisdn;

if (!function_exists('msisdn')) {

    function msisdn(): Msisdn
    {
        return resolve(Msisdn::class);
    }
}
