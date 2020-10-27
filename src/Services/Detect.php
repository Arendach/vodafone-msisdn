<?php

namespace Vodafone\Msisdn\Services;

class Detect
{
    private $storageService;

    public function __construct()
    {
        $this->storageService = app(MsisdnStorageService::class);
    }

    public function detect(): bool
    {
        return $this->isDetectedName() && $this->isDetectedMsisdn() && !$this->isChangedIp();
    }

    public function needRequest(): bool
    {
        return (!$this->isDetectedName() || !$this->isDetectedMsisdn()) && $this->isChangedIp();
    }

    public function isDetectedMsisdn(): bool
    {
        return !is_null(
            $this->storageService->getMsisdn()
        );
    }

    public function isDetectedName(): bool
    {
        $language = $this->getLanguage();

        return !is_null(
            $this->storageService->getName($language)
        );
    }

    private function isChangedIp(): bool
    {
        $ip = request()->ip();
        $ips = $this->storageService->getIps();

        if (isset($ips[$ip])) {
            return false;
        }

        return true;
    }

    private function getLanguage(): string
    {
        return app()->getLocale() == 'ru' ? 'ru' : 'uk';
    }
}
