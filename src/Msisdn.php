<?php

declare(strict_types=1);

namespace Vodafone\Msisdn;

use MultiSessions\Session;
use Psr\SimpleCache\InvalidArgumentException;
use Vodafone\Msisdn\Services\Decrypt;
use Vodafone\Msisdn\Services\Hmac;

class Msisdn
{
    private $decrypt;
    private $hmac;
    private $useCache = false;
    private $cacheStorage;

    public function __construct()
    {
        $this->decrypt = new Decrypt;
        $this->hmac = new Hmac;
    }

    /**
     * @param bool $useCache
     * @return $this
     */
    public function cache(bool $useCache = false): self
    {
        $this->useCache = $useCache;

        return $this;
    }

    /**
     * @param string $msisdn
     * @param string $hmac
     * @return string|null
     * @throws Exceptions\DecryptException
     * @throws Exceptions\HmacException
     */
    public function init(string $msisdn, string $hmac): ?string
    {
        if (!$this->checkHmac($msisdn, $hmac)) {
            return null;
        }

        $phone = $this->decryptMsisdn($msisdn);

        if ($this->useCache) {
            $this->saveToCache($phone);
        }

        return $phone;
    }

    /**
     * @return int
     * @throws InvalidArgumentException
     */
    public function getStatus(): int
    {
        $phoneStatus = $this->getSessionStorage()->get('phone_status');

        if (is_null($phoneStatus)) {
            return 0;
        }

        return $phoneStatus;
    }

    /**
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function getPhone(): ?string
    {
        return $this->getSessionStorage()->get('phone');
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

    /**
     * @param string|null $phone
     */
    private function saveToCache(?string $phone): void
    {
        $this->getSessionStorage()
            ->set('phone', $phone)
            ->set('phone_status', $phone ? 1 : -1);
    }

    private function getSessionStorage(): Session
    {
        if (!$this->cacheStorage) {
            $this->cacheStorage = new Session('personification');
        }

        return $this->cacheStorage;
    }
}
