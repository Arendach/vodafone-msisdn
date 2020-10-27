<?php

namespace Vodafone\Msisdn\Services;

use Exception;

class Decrypt
{
    private $secret;
    private $IV;

    public function __construct()
    {
        $this->secret = $this->getSecret();
        $this->IV = $this->getIV();
    }

    public function decrypt(string $encryptedString): ?string
    {
        $method = setting('Метод дешифрування x-msisdn (rc4|aes256)', 'aes256');

        if ($method == 'aes256') {

            return $this->decryptAES256($encryptedString);

        } elseif ($method == 'rc4') {

            return $this->decryptRC4($encryptedString);

        }

        return null;
    }

    private function decryptRC4(string $encryptedString): ?string
    {
        try {

            $secret = md5($this->secret, true);
            $decodedString = base64_decode($encryptedString);

            return rc4($secret, $decodedString);

        } catch (Exception $exception) {

            return null;

        }
    }

    private function decryptAES256(string $encrypted): ?string
    {
        try {

            $secret = $this->secret;
            $iv = $this->IV;

            $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $secret, OPENSSL_ZERO_PADDING, $iv);

            return trim($decrypted);

        } catch (Exception $exception) {

            \Log::info($exception->getMessage());

            return null;

        }
    }

    private function getSecret(): ?string
    {
        return env('MSISDN_SECRET', 'test');
    }

    private function getIV(): string
    {
        return env('MSISDN_IV', '1234567890123456');
    }
}
