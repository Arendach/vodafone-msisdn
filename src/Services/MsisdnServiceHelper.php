<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn\Services;

class MsisdnServiceHelper
{
    public static function getSecret(?string $source): ?string
    {
        if (self::base64detect($source)) {
            return base64_decode($source);
        }

        return $source;
    }

    public static function base64detect(?string $source): bool
    {
        if (!$source) {
            return false;
        }
        
        return (bool)preg_match('~^([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)?$~', $source);
    }
}