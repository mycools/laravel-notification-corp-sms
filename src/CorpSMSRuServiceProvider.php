<?php

namespace NotificationChannels\CorpSMS;

use Illuminate\Support\ServiceProvider;

class CorpSMSRuServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CorpSMSApi::class, function ($app) {
            return new CorpSMSApi($app['config']['services.CorpSMS']);
        });
    }
}
