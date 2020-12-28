<?php

declare(strict_types=1);

namespace Vodafone\Msisdn\Providers;

use Illuminate\Support\ServiceProvider;
use Vodafone\Msisdn\Msisdn;
use Vodafone\Msisdn\Services\Logger;
use Illuminate\Support\Facades\Config;

class VodafoneMsisdnServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../Config/vodafone-msisdn.php' => config_path('vodafone-msisdn.php'),
        ], 'vodafone-msisdn');
    }

    public function register(): void
    {
        Config::set('logging.channels.msisdn', [
            'driver' => 'daily',
            'path'   => storage_path('logs/msisdn.log'),
            'level'  => 'debug',
        ]);

        $this->app->singleton(Msisdn::class, function ($app) {
            return new Msisdn;
        });

        $this->app->singleton(Logger::class, function ($app) {
            return new Logger;
        });
    }
}