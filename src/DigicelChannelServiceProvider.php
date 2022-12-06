<?php

namespace Metarelic\Notifications;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use Metarelic\Notifications\Channels\DigicelSMSChannel;

class DigicelChannelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/digicel.php', 'digicel');

        $this->app->bind(DigicelSMSChannel::class, function ($app) {
            return new DigicelSMSChannel(
                $app['config']['digicel.token'],
                $app['config']['digicel.from']
            );
        });


        Notification::resolved(function (ChannelManager $service) {
            $service->extend('digicel', function ($app) {
                return $app->make(DigicelSMSChannel::class);
            });
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/digicel.php' => $this->app->configPath('digicel.php'),
            ], 'digicel');
        }
    }
}