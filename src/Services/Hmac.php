<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn\Services;

use Exception;
use Arendach\VodafoneMsisdn\Exceptions\HmacException;
use Debugbar;

class Hmac
{
    private $secret;
    private $algo;
    private $logger;
    private $isDebug;
    private $isThrowException;

    /**
     * Hmac constructor.
     */
    public function __construct()
    {
        $this->secret = MsisdnServiceHelper::getSecret(config('vodafone-msisdn.hmac-secret'));
        $this->algo = config('vodafone-msisdn.hmac-algo');
        $this->isDebug = config('vodafone-msisdn.debug-mode');
        $this->isThrowException = config('vodafone-msisdn.throw-exception');
        $this->logger = resolve(Logger::class);
    }

    /**
     * @param string $msisdn
     * @param string $hmac
     * @return bool
     * @throws HmacException
     */
    public function check(string $msisdn, string $hmac): bool
    {
        if ($this->isDebug) {
            Debugbar::info("Msisdn -> «{$msisdn}», Hmac -> «{$hmac}»");
        }

        try {

            $algo = $this->algo;
            $secret = $this->secret;

            $hashGenerated = hash_hmac($algo, $msisdn, $secret, true);
            $hashGenerated = base64_encode($hashGenerated);
            $result = $hashGenerated == $hmac;

            if (!$result) {
                throw new Exception("HMAC testing failed! HMAC hash: {$hmac}, MSISDN: {$msisdn}");
            }

            return true;

        } catch (Exception $exception) {

            $this->logger->save($exception->getMessage());

            if ($this->isThrowException) {
                throw new HmacException($exception->getMessage());
            }

            return false;

        }
    }
}
