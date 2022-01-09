<?php

namespace App\Providers;

use App\Services\Sync\Task;
use App\Settings\DarkMode;
use App\Settings\StravaClient;
use App\Settings\UnitSystem;
use App\Tasks\CreateBackupTask;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use maxh\Nominatim\Nominatim;
use Settings\Setting;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('nominatim', fn() => new Nominatim('https://nominatim.openstreetmap.org/'));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('external', function ($app, $config) {
            // Get the preferred disk, one that has space on it?

            return Storage::disk('s3');
        });

        Task::registerTask(CreateBackupTask::class);

        Setting::register(new UnitSystem());
        Setting::register(new DarkMode());
        Setting::register(new StravaClient());
    }
}
