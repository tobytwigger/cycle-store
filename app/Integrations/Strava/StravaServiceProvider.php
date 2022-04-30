<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Client\Commands\ResetRateLimit;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;

class StravaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([ResetRateLimit::class]);
//        $this->app->singleton(StravaImporter::class);
    }

    public function boot()
    {
//        $this->commands([
//            ResetRateLimit::class,
//            SetupWebhooks::class
//        ]);
//        if(config('strava.enable_detail_fetching', true)) {

            // Load segments and additional stats for an activity
//            Event::listen(StravaActivityUpdated::class, MarkActivityAsLoadingDetails::class);
//            Event::listen(StravaActivityUpdated::class, IndexStravaActivity::class);

            // Load comments for an activity
//            Event::listen(StravaActivityCommentsUpdated::class, MarkActivityAsLoadingComments::class);
//            Event::listen(StravaActivityCommentsUpdated::class, IndexStravaActivityComments::class);

            // Load kudos for an activity
//            Event::listen(StravaActivityKudosUpdated::class, MarkActivityAsLoadingKudos::class);
//            Event::listen(StravaActivityKudosUpdated::class, IndexStravaActivityKudos::class);

            // Load photos for an activity
//            Event::listen(StravaActivityPhotosUpdated::class, MarkActivityAsLoadingPhotos::class);
//            Event::listen(StravaActivityPhotosUpdated::class, IndexStravaActivityPhotos::class);
//        }

//        Integration::registerIntegration('strava', StravaIntegration::class);
//        Importer::registerImporter(ActivityImporter::class);
//        Importer::registerImporter(PhotoImporter::class);
//        Task::registerTask(SaveAllActivities::class);
//        Task::registerTask(StravaUpload::class);

//        Route::middleware(['web', 'auth:sanctum', 'verified'])->group(function() {
//            Route::resource('import', ImportController::class)->only('show');
//        });
//        Route::middleware(['web', 'auth:sanctum', 'verified'])->prefix('strava')->group(function() {
//            Route::get('client/{client}/login', [StravaController::class, 'login'])->name('strava.login');
//            Route::get('client/{client}/callback', [StravaController::class, 'callback'])->name('strava.callback');
//            Route::post('/client/{client}/invite', [ClientInvitationController::class, 'invite'])->name('strava.client.invite');
//            Route::middleware('link')->get('/client/{client}/accept', [ClientInvitationController::class, 'accept'])->name('strava.client.accept');
//            Route::delete('/client/{client}/leave', [ClientInvitationController::class, 'leave'])->name('strava.client.leave');
//            Route::post('/client/{client}/enable', [ClientStatusController::class, 'enable'])->name('strava.client.enable');
//            Route::post('/client/{client}/disable', [ClientStatusController::class, 'disable'])->name('strava.client.disable');
//            Route::post0('/client/{client}/public', [ClientStatusController::class, 'makePublic'])->name('strava.client.public');
//            Route::post('/client/{client}/private', [ClientStatusController::class, 'makePrivate'])->name('strava.client.private');
//            Route::resource('client', ClientController::class, ['as' => 'strava'])->only('index', 'store', 'destroy');
//        });
//        Route::middleware(['webhooks'])->prefix('strava/webhook/incoming')->group(function() {
//            Route::get('/client/{client}', [IncomingWebhookController::class, 'verify'])->name('strava.webhook.verify');
//            Route::pos0t('/client/{client}', [IncomingWebhookController::class, 'incoming'])->name('strava.webhook.incoming');
//        });
//
//        RateLimiter::for('strava', fn($job) => static::stravaLimiters());
    }

    public static function stravaLimiters(): array
    {
        return [
            Limit::perMinutes(15, 70)->by('strava-15-mins'),
            Limit::perDay(900)->by('strava-daily'),
        ];
    }
}
