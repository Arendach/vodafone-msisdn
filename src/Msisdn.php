<?php

declare(strict_types=1);

namespace Vodafone\Msisdn;

use Vodafone\Msisdn\Services\Decrypt;
use Vodafone\Msisdn\Services\Hmac;

class Msisdn
{
    private $decrypt;
    private $hmac;

    public function __construct()
    {
        $this->decrypt = new Decrypt;
        $this->hmac = new Hmac;
    }

    /**
     * @param string $msisdn
     * @param string $hmac
     * @return string|null
     * @throws Exceptions\DecryptException
     * @throws Exceptions\HmacException
     */
    public function get(string $msisdn, string $hmac): ?string
    {
        if (!$this->checkHmac($msisdn, $hmac)) {
            return null;
        }

         return $this->decryptMsisdn($msisdn);
    }

    /**
     * @param string $msisdn
     * @param string $hmac
     * @return bool
     * @throws Exceptions\HmacException
     */
    private function checkHmac(string $msisdn, string $hmac): bool
    {
        return $this->hmac->check($msisdn, $hmac);
    }

    /**
     * @param string $msisdn
     * @return string|null
     * @throws Exceptions\DecryptException
     */
    private function decryptMsisdn(string $msisdn): ?string
    {
        return $this->decrypt->decrypt($msisdn);
    }
}
