<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn\Services;

class Encrypt
{
    private $msisdn;
    private $hmac;

    public function make(string $source): void
    {
        $this->msisdn = $this->makeMsisdn($source);
        $this->hmac = $this->makeHmac($this->msisdn);
    }

    public function getMsisdn(): string
    {
        return $this->msisdn;
    }

    public function getHmac(): string
    {
        return $this->hmac;
    }

    private function makeMsisdn(string $phone): string
    {
        $algo = config('vodafone-msisdn.decrypt-algo');
        $secret = config('vodafone-msisdn.decrypt-secret');
        $iv = config('vodafone-msisdn.decrypt-iv');

        $phone = $this->makePadding($phone, $iv);

        return openssl_encrypt($phone, $algo, $secret, OPENSSL_ZERO_PADDING, $iv);
    }

    private function makeHmac(string $msisdn): string
    {
        $secret = MsisdnServiceHelper::getSecret(config('vodafone-msisdn.hmac-secret'));
        $algo = config('vodafone-msisdn.hmac-algo');

        return base64_encode(hash_hmac($algo, $msisdn, $secret, true));
    }

    private function makePadding(string $source, string $iv): string
    {
        $ivLength = strlen($iv);

        if (strlen($source) % $ivLength) {
            return str_pad($source, strlen($source) + $ivLength - strlen($source) % $ivLength, "\0");
        }

        return $source;
    }
}