<?php

namespace App\Providers;

use App\Services\Proxy\ProxyService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app
            ->when(ProxyService::class)
            ->needs('$baseUri')
            ->give(config('proxy_list.base_uri'));

        $this->app
            ->when(ProxyService::class)
            ->needs(Client::class)
            ->give(function () {
                return new Client([
                    'http_errors' => true,
                ]);
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
