<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn\Services;

use Exception;
use Arendach\VodafoneMsisdn\Exceptions\DecryptException;

class Decrypt
{
    private $secret;
    private $iv;
    private $algo;
    private $logger;

    public function __construct()
    {
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
        try {

            $secret = $this->secret;
            $iv = $this->iv;
            $algo = $this->algo;

            $decrypted = openssl_decrypt($encryptedString, $algo, $secret, OPENSSL_ZERO_PADDING, $iv);

            return trim($decrypted);

        } catch (Exception $exception) {

            $this->logger->save("Decrypt msisdn failed! Encrypted string: {$encryptedString}, algo: {$this->algo}, secret: {$this->secret}, iv: {$this->iv}");

            throw new DecryptException("Decrypt msisdn failed! Encrypted string: {$encryptedString}");

        }
    }
}
