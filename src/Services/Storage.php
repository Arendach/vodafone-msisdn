<?php

namespace App\Services\Msisdn;

class Storage
{
    private $msisdn;
    private $nameUk;
    private $nameRu;
    private $ips = [];

    public function __construct()
    {
        $this->loadMsisdn();
        $this->loadName();
        $this->loadIps();
    }

    public function setMsisdn(string $msisdn): void
    {
        $this->msisdn = $msisdn;

        session()->put('account_msisdn', $msisdn);
    }

    public function setName(string $name, string $language): void
    {
        if ($language == 'uk') {
            $this->nameUk = $name;
        } else {
            $this->nameRu = $name;
        }

        session()->put("account_name_{$language}", $name);
    }

    public function setStatusForIp(string $ip, int $flag): void
    {
        $this->ips[$ip] = $flag;

        session()->put('account_ip_statuses', $this->ips);
    }

    public function getStatusForIp(string $ip): int
    {
        return $this->ips[$ip] ?? 0;
    }

    public function getName(string $language): ?string
    {
        return $language == 'uk' ? $this->nameUk : $this->nameRu;
    }

    public function getMsisdn(): ?string
    {
        return $this->msisdn;
    }

    public function getIps(): array
    {
        return $this->ips;
    }


    private function loadName(): void
    {
        if (session()->has('account_name_uk')) {
            $this->nameUk = session()->get('account_name_uk');
        }

        if (session()->has('account_name_ru')) {
            $this->nameRu = session()->get('account_name_ru');
        }
    }

    private function loadMsisdn(): void
    {
        if (session()->has('account_msisdn')) {
            $this->msisdn = session()->get('account_msisdn');
        }
    }

    private function loadIps(): void
    {
        if (session()->has('account_ip_statuses')) {
            $this->ips = session()->get('account_ip_statuses');
        }
    }
}
