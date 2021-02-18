<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn;

use Arendach\MultiSessions\Session;
use Psr\SimpleCache\InvalidArgumentException;
use Arendach\VodafoneMsisdn\Services\Decrypt;
use Arendach\VodafoneMsisdn\Services\Hmac;

class Msisdn
{
    /**
     * @var Decrypt
     */
    private $decryptService;

    /**
     * @var Hmac
     */
    private $hmacService;

    /**
     * @var Session
     */
    private $cacheStorage;

    /**
     * @var string|null
     */
    private $phone;

    /**
     * @var int
     */
    private $phoneStatus;

    /**
     * Msisdn constructor.
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->decryptService = new Decrypt;
        $this->hmacService = new Hmac;
        $this->cacheStorage = Session::instance('personification');

        $this->loading();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function loading(): void
    {
        $this->phone = $this->cacheStorage->get('phone');

        $phoneStatus = $this->cacheStorage->get('phone_status');
        $this->phoneStatus = in_array($phoneStatus, [1, -1]) ? $phoneStatus : 0;
    }

    /**
     * @param string $msisdn
     * @param string $hmac
     * @return string|null
     * @throws Exceptions\DecryptException
     * @throws Exceptions\HmacException
     */
    public function decrypt(string $msisdn, string $hmac): ?string
    {
        if (!$this->checkHmac($msisdn, $hmac)) {
            return null;
        }

        return $this->decryptMsisdn($msisdn);
    }

    /**
     * @param string|null $msisdn
     * @param string|null $hmac
     * @return string|null
     * @throws Exceptions\DecryptException
     * @throws Exceptions\HmacException
     */
    public function decryptAndSave(?string $msisdn, ?string $hmac): ?string
    {
        $phone = (!$msisdn || !$hmac) ? null : $this->decrypt($msisdn, $hmac);

        $this->saveToCache($phone);

        $this->phone = $phone;
        $this->phoneStatus = $phone ? 1 : -1;

        return $phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->saveToCache($phone);

        $this->phone = $phone;
        $this->phoneStatus = 1;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->phoneStatus;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $msisdn
     * @param string $hmac
     * @return bool
     * @throws Exceptions\HmacException
     */
    private function checkHmac(string $msisdn, string $hmac): bool
    {
        return $this->hmacService->check($msisdn, $hmac);
    }

    /**
     * @param string $msisdn
     * @return string|null
     * @throws Exceptions\DecryptException
     */
    private function decryptMsisdn(string $msisdn): ?string
    {
        return $this->decryptService->decrypt($msisdn);
    }

    /**
     * @param string|null $phone
     */
    private function saveToCache(?string $phone): void
    {
        $this->cacheStorage
            ->set('phone', $phone)
            ->set('phone_status', $phone ? 1 : -1);
    }

    public function rebootSession(): void
    {
        if ($this->cacheStorage->get('phone_status') == -1) {
            $this->cacheStorage->set('phone_status', 0);
        }
    }
}
