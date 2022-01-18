<?php

namespace Tests\Unit\Stats;

use App\Jobs\AnalyseFile;
use App\Models\Activity;
use App\Models\File;
use App\Models\Route;
use Tests\TestCase;

class AnalyseFileTest extends TestCase
{

    /** @test */
    public function it_throws_an_exception_if_an_activity_is_missing_a_file(){
        $activity = Activity::factory()->create(['file_id' => null]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Activity ' . $activity->id . ' does not have a model associated with it.');

        $job = new AnalyseFile($activity);
        $job->handle();
    }

    /** @test */
    public function it_throws_an_exception_if_a_route_is_missing_a_file(){
        $route = Route::factory()->create(['file_id' => null]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route ' . $route->id . ' does not have a model associated with it.');

        $job = new AnalyseFile($route);
        $job->handle();
    }

    /** @test */
    public function it_creates_stats_for_an_activity(){
        $activity = Activity::factory()->create([
            'file_id' => File::factory()->routeFile()->create()->id
        ]);

        $job = new AnalyseFile($activity);
        $job->handle();

        $this->assertDatabaseHas('stats', [
            'stats_id' => $activity->id,
            'stats_type' => Activity::class
        ]);
    }

    /** @test */
    public function it_creates_stats_for_a_route(){
        $route = Route::factory()->create([
            'file_id' => File::factory()->routeFile()->create()->id
        ]);

        $job = new AnalyseFile($route);
        $job->handle();

        $this->assertDatabaseHas('stats', [
            'stats_id' => $route->id,
            'stats_type' => Route::class
        ]);
    }

}
