<?php

namespace Creacoon\NetdataTile;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class NetdataTileServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchDataFromNetdataCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/dashboard-netdata-tile'),
        ], 'dashboard-netdata-tile-views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dashboard-netdata-tile');

        Livewire::component('netdata-tile', NetdataTileComponent::class);
    }
}
