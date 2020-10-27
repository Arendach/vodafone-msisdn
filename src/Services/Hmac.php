<?php

namespace App\Services\Msisdn;

use Exception;
use Log;

class Hmac
{
    private $secret;
    private $isDebug;

    public function __construct()
    {
        $this->secret = $this->getSecret();
        $this->isDebug = $this->getDebug();
    }

    public function check(string $hashHmac, string $msisdnEncrypted): bool
    {
        try {
            $algo = 'sha256';
            $secret = $this->secret;

            $hashGenerated = hash_hmac($algo, $msisdnEncrypted, $secret, true);
            $hashGenerated = base64_encode($hashGenerated);

            if ($this->isDebug) {
                $this->debugLogging($msisdnEncrypted, $hashHmac);
            }

            return $hashGenerated == $hashHmac;

        } catch (Exception $exception) {

            Log::error('Error check HMAC hash -> ' . $exception->getMessage());

            return false;

        }

    }

    private function getSecret(): ?string
    {
        $secret = env('MSISDN_HMAC_SECRET', 'Uk0SrLnUH8K4X1v6j1nrgG4bCTBpaKFGnRW4Eo+kJ1M=');

        return base64_decode($secret);
    }

    private function getDebug(): bool
    {
        return env('APP_DEBUG', false);
    }

    private function debugLogging($msisdn, $hmac): void
    {
        Log::info("HMAC check debug: msisdn - «{$msisdn}», hmac hash - «{$hmac}»");
    }
}
