<?php

namespace App\Services;

use App\Services\Msisdn\MsisdnDecryptService;
use App\Services\Msisdn\MsisdnDetectService;
use App\Services\Msisdn\MsisdnHmacService;
use App\Services\Msisdn\MsisdnNameService;
use App\Services\Msisdn\MsisdnStorageService;

class MsisdnService
{
    private $storageService;
    private $decryptService;
    private $nameService;
    private $hmacService;
    private $detectService;

    public function __construct()
    {
        $this->storageService = app(MsisdnStorageService::class);
        $this->decryptService = app(MsisdnDecryptService::class);
        $this->nameService = app(MsisdnNameService::class);
        $this->hmacService = app(MsisdnHmacService::class);
        $this->detectService = app(MsisdnDetectService::class);
    }

    public function isDetected(): bool
    {
        return $this->detectService->detect();
    }

    public function getClientName(): ?string
    {
        $language = $this->getLanguage();

        return $this->storageService->getName($language);
    }

    public function needRequest(): bool
    {
        return $this->detectService->needRequest();
    }

    public function getClientNumber(): ?string
    {
        return $this->storageService->getMsisdn();
    }

    public function getStatus(?string $ip = null): int
    {
        $ips = $this->storageService->getIps();

        foreach ($ips as $ipKey => $status) {
            if ($status == 1) {
                return 1;
            }
        }

        if (is_null($ip)) {
            $ip = request()->ip();
        }

        return $this->storageService->getStatusForIp($ip);
    }

    public function init(?string $msisdn, ?string $hmacHash, string $ip): void
    {
        // if empty msisdn
        if (is_null($msisdn) or is_null($hmacHash)) {
            $this->storageService->setStatusForIp($ip, -1);

            return;
        }

        $hmacTest = $this->hmacService->check($hmacHash, $msisdn);

        if (!$hmacTest) {
            $this->storageService->setStatusForIp($ip, -1);

            return;
        }

        $msisdn = $this->decryptService->decrypt($msisdn);

        // if not decrypt
        if (!$msisdn) {
            $this->storageService->setStatusForIp($ip, -1);

            return;
        }

        $this->storageService->setMsisdn($msisdn);

        $nameUk = $this->nameService->search($msisdn, 'uk');
        $nameRu = $this->nameService->search($msisdn, 'ru');

        // if user not found in database
        if (!$nameUk || !$nameRu) {
            $this->storageService->setStatusForIp($ip, -1);

            return;
        }

        $this->storageService->setName($nameUk, 'uk');
        $this->storageService->setName($nameRu, 'ru');

        $this->storageService->setStatusForIp($ip, 1);
    }

    private function getLanguage(): string
    {
        return app()->getLocale() == 'ru' ? 'ru' : 'uk';
    }
}
