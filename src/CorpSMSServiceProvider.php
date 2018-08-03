<?php

namespace NotificationChannels\CorpSMS;

use Illuminate\Support\ServiceProvider;

class CorpSMSServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CorpSMSApi::class, function ($app) {
            return new CorpSMSApi($app['config']['services.corpsms']);
        });
    }
}
