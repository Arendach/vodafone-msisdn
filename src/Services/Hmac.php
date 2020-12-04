<?php

declare(strict_types=1);

namespace Vodafone\Msisdn\Services;

use Exception;
use Vodafone\Msisdn\Exceptions\HmacException;

class Hmac
{
    private $secret;
    private $algo;
    private $logger;

    /**
     * Hmac constructor.
     */
    public function __construct()
    {
        $this->secret = base64_decode(config('vodafone-msisdn.hmac-secret'));
        $this->algo = config('vodafone-msisdn.hmac-algo');
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

            throw new HmacException($exception->getMessage());

        }
    }
}
