<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn\Services;

use Debugbar;
use Exception;
use Arendach\VodafoneMsisdn\Exceptions\DecryptException;

class Decrypt
{
    private $secret;
    private $iv;
    private $algo;
    private $logger;
    private $isDebug;
    private $isThrowException;

    public function __construct()
    {
        $this->isDebug = config('vodafone-msisdn.debug-mode');
        $this->isThrowException = config('vodafone-msisdn.throw-exception');
        $this->secret = config('vodafone-msisdn.decrypt-secret');
        $this->iv = config('vodafone-msisdn.decrypt-iv');
        $this->algo = config('vodafone-msisdn.decrypt-algo');
        $this->logger = resolve(Logger::class);
    }

    /**
     * @param string $encryptedString
     * @return string|null
     * @throws DecryptException
     */
    public function decrypt(string $encryptedString): ?string
    {
        if ($this->isDebug) {
            Debugbar::info("Encrypted string -> «{$encryptedString}»");
        }

        try {

            $secret = $this->secret;
            $iv = $this->iv;
            $algo = $this->algo;

            $decrypted = openssl_decrypt($encryptedString, $algo, $secret, OPENSSL_ZERO_PADDING, $iv);

            $decrypted = trim($decrypted);

            return $this->isValid($decrypted) ? $decrypted : null;

        } catch (Exception $exception) {

            $this->logger->save("Decrypt msisdn failed! Encrypted string: {$encryptedString}, algo: {$this->algo}, secret: {$this->secret}, iv: {$this->iv}");

            if ($this->isThrowException) {
                throw new DecryptException("Decrypt msisdn failed! Encrypted string: {$encryptedString}");
            }

            return null;

        }
    }

    private function isValid(?string $phone): bool
    {
        if (is_null($phone) || !preg_match('~380[0-9]{9}~', $phone)) {
            return false;
        }

        return true;
    }
}
