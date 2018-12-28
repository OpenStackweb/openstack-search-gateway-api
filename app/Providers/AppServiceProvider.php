<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\NativeMailerHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $logger = Log::getLogger();
        foreach($logger->getHandlers() as $handler) {
            $handler->setLevel(Config::get('log.level', 'debug'));
        }

        //set email log
        $to   = Config::get('log.to_email', '');
        $from = Config::get('log.from_email', '');

        if (!empty($to) && !empty($from)) {
            $subject = 'openstackid-resource-server error';
            $handler = new NativeMailerHandler($to, $subject, $from);
            $handler->setLevel(Config::get('log.email_level', 'error'));
            $logger->pushHandler($handler);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
