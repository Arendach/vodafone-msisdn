<?php

declare(strict_types=1);

namespace Arendach\VodafoneMsisdn\Services;

use Log;
use Exception;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var bool
     */
    private $isDebug = false;

    /**
     * Logger constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->log = Log::channel('msisdn');
        $this->isDebug = config('vodafone-msisdn.debug-mode');
    }

    /**
     * @param string $message
     */
    public function save(string $message): void
    {
        if (!$this->isDebug) {
            return;
        }

        $this->log->info($message);
    }
}