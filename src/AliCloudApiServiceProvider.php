<?php

namespace Zhan3333\AliCloudApi;

use Illuminate\Support\ServiceProvider;

class AliCloudApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/alicloudapi.php' => config_path('alicloudapi.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/alicloudapi.php', 'alicloudapi'
        );

        $this->app->singleton(AliVerifyIdCardService::class, function () {
            return new AliVerifyIdCardService(
                config('alicloudapi.verify_id_card.app_code'),
                config('alicloudapi.verify_id_card.log_channel'),
                config('alicloudapi.verify_id_card.fake_type'),
            );
        });
    }
}
