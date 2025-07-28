<?php

namespace App\Providers;

use App\Events\WaterSourceCreated;
use App\Services\LoggerService;
use Illuminate\Support\ServiceProvider;
use App\Listeners\LogWaterSourceCreation;
use MatanYadaev\EloquentSpatial\EloquentSpatial;
use MatanYadaev\EloquentSpatial\Enums\Srid;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [

        WaterSourceCreated::class => [
            LogWaterSourceCreation::class,
        ],
    ];
    public function register(): void
    {
        $this->app->singleton(LoggerService::class, function () {
            return new LoggerService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        EloquentSpatial::setDefaultSrid(Srid::WGS84);
    }
}
