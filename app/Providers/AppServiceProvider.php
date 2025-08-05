<?php

namespace App\Providers;

use App\Services\LoggerService;
use App\Events\WaterSourceCreated;
use Illuminate\Support\ServiceProvider;
use App\Events\ReportGenerationRequested;
use App\Listeners\DispatchReportEmailJob;
use App\Listeners\LogWaterSourceCreation;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\EloquentSpatial;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [

    WaterSourceCreated::class => [
            LogWaterSourceCreation::class,
        ]
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
