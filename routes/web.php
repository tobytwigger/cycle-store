<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\Pages\PublicController::class, 'welcome'])->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/dashboard', [\App\Http\Controllers\Pages\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('activity', \App\Http\Controllers\Pages\ActivityController::class)->except(['edit']);

    Route::resource('sync', \App\Http\Controllers\Pages\SyncController::class)->only(['index', 'store', 'destroy']);
    Route::get('file/{file}/download', [\App\Http\Controllers\Pages\FileController::class, 'download'])->name('file.download');
    Route::get('file/{file}/preview', [\App\Http\Controllers\Pages\FileController::class, 'preview'])->name('file.preview');
    Route::redirect('/upload', route('activity.create'))->name('upload');
    Route::get('/documentation', fn() => \Illuminate\Support\Facades\Redirect::away('https://tobytwigger.github.io/cycle-store/'))->name('documentation');

    Route::get('/activity/stats/{stats}/chart', [\App\Http\Controllers\Api\StatsController::class, 'chart'])->name('activity.stats.chart');
    Route::get('/activity/stats/{stats}/geojson', [\App\Http\Controllers\Api\StatsController::class, 'geojson'])->name('activity.stats.geojson');
    Route::post('/activity/file/duplicate', [\App\Http\Controllers\Api\ActivityDuplicateController::class, 'index'])->name('activity.file.duplicate');

    Route::resource('backups', \App\Http\Controllers\Pages\BackupController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['backups' => 'file']);
    Route::delete('backups/sync/{sync}/cancel', [\App\Http\Controllers\Pages\BackupController::class, 'cancelSync'])->name('backups.sync.cancel');

    Route::resource('settings', \App\Http\Controllers\Pages\SettingsController::class)->only(['index', 'store']);

    Route::resource('route', \App\Http\Controllers\Pages\RouteController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::prefix('route/{route}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\RouteDownloadController::class, 'downloadRoute'])->name('route.download');
        Route::resource('file', \App\Http\Controllers\Pages\RouteFileController::class, ['as' => 'route'])->only(['destroy', 'update', 'store']);
    });
    Route::post('/route/file/duplicate', [\App\Http\Controllers\Api\RouteDuplicateController::class, 'index'])->name('route.file.duplicate');
    Route::get('/route/stats/{stats}/geojson', [\App\Http\Controllers\Api\StatsController::class, 'geojsonRoute'])->name('route.stats.geojson');

    Route::prefix('activity/{activity}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\ActivityDownloadController::class, 'downloadActivity'])->name('activity.download');
        Route::resource('file', \App\Http\Controllers\Pages\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update', 'store']);
    });

    Route::post('/data/download', [\App\Http\Controllers\Pages\ActivityDownloadController::class, 'all'])->name('data.download');

    Route::get('/integration/{integration}/login', [\App\Http\Controllers\Pages\IntegrationLoginController::class, 'login'])->name('integration.login');
    Route::delete('/integration/{integration}', [\App\Http\Controllers\Pages\IntegrationController::class, 'destroy'])->name('integration.destroy');
    Route::get('/integration/{integration}/logs', [\App\Http\Controllers\Pages\ConnectionLogController::class, 'index'])->name('integration.logs');

});
