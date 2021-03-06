<?php

namespace App\Integrations\Strava\Client\Import\Resources;

use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Integrations\Strava\Jobs\LoadStravaComments;
use App\Integrations\Strava\Jobs\LoadStravaKudos;
use App\Integrations\Strava\Jobs\LoadStravaPhotos;
use App\Integrations\Strava\Jobs\LoadStravaStats;
use App\Models\Activity as ActivityModel;
use App\Models\Stats;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class Activity
{
    public const CREATED = 'created';

    public const UPDATED = 'updated';

    private ?string $status = null;

    private ActivityModel $activity;

    public function getActivity(): ActivityModel
    {
        return $this->activity;
    }

    public function import(array $activityData, User $user): Activity
    {
        $existingActivity = $this->getExistingActivity($activityData);

        $this->activity = $existingActivity === null
            ? $this->createActivity($activityData, $user)
            : $this->updateActivity($activityData, $existingActivity);

        return $this;
    }

    public function getExistingActivity(array $activityData): ?ActivityModel
    {
        // Try and get the existing activity by its ID
        return array_key_exists('id', $activityData)
            ? ActivityModel::whereAdditionalData('strava_id', data_get($activityData, 'id'))->first()
            : null;
    }

    private function getIntegerData(array $data, string $key)
    {
        return array_key_exists($key, $data) ? (int) $data[$key] : null;
    }

    private function createActivity(array $activityData, User $user): ActivityModel
    {
        $this->status = self::CREATED;

        $activity = ActivityImporter::for($user)
            ->withName(data_get($activityData, 'name'))
            ->linkTo('strava')
            ->setAdditionalData('strava_id', $this->getIntegerData($activityData, 'id'))
            ->setAdditionalData('strava_upload_id', $this->getIntegerData($activityData, 'upload_id_str'))
            ->setAdditionalData('strava_photo_count', $this->getIntegerData($activityData, 'total_photo_count'))
            ->setAdditionalData('strava_comment_count', $this->getIntegerData($activityData, 'comment_count'))
            ->setAdditionalData('strava_kudos_count', $this->getIntegerData($activityData, 'kudos_count'))
            ->setAdditionalData('strava_pr_count', $this->getIntegerData($activityData, 'pr_count'))
            ->setAdditionalData('strava_achievement_count', $this->getIntegerData($activityData, 'achievement_count'))
            ->import();

        $this->fillStats($activityData, new Stats(['stats_id' => $activity->id, 'stats_type' => $activity::class]))->save();

        LoadStravaActivity::dispatch($activity);
        LoadStravaStats::dispatch($activity);

        if ($this->getIntegerData($activityData, 'comment_count') > 0) {
            LoadStravaComments::dispatch($activity);
        }
        if ($this->getIntegerData($activityData, 'kudos_count') > 0) {
            LoadStravaKudos::dispatch($activity);
        }
        if ($this->getIntegerData($activityData, 'total_photo_count') > 0) {
            LoadStravaPhotos::dispatch($activity);
        }

        return $activity;
    }

    private function updateActivity(array $activityData, ActivityModel $existingActivity): ActivityModel
    {
        $importer = ActivityImporter::update($existingActivity)
            ->linkTo('strava');
        $updated = [];

        if (array_key_exists('upload_id_str', $activityData) && $existingActivity->getAdditionalData('strava_upload_id') !== data_get($activityData, 'upload_id_str')) {
            $importer->setAdditionalData('strava_upload_id', data_get($activityData, 'upload_id_str'));
            $updated[] = 'details';
        }

        if ($existingActivity->getAdditionalData('strava_photo_count') !== $this->getIntegerData($activityData, 'total_photo_count')) {
            $importer->setAdditionalData('strava_photo_count', $this->getIntegerData($activityData, 'total_photo_count'));
            $updated[] = 'photos';
        }

        if ($existingActivity->getAdditionalData('strava_comment_count') !== $this->getIntegerData($activityData, 'comment_count')) {
            $importer->setAdditionalData('strava_comment_count', $this->getIntegerData($activityData, 'comment_count'));
            $updated[] = 'comments';
        }

        if ($existingActivity->getAdditionalData('strava_kudos_count') !== $this->getIntegerData($activityData, 'kudos_count')) {
            $importer->setAdditionalData('strava_kudos_count', $this->getIntegerData($activityData, 'kudos_count'));
            $updated[] = 'kudos';
        }

        if ($existingActivity->getAdditionalData('strava_pr_count') !== $this->getIntegerData($activityData, 'pr_count')
            || $existingActivity->getAdditionalData('strava_achievement_count') !== $this->getIntegerData($activityData, 'achievement_count')) {
            $importer
                ->setAdditionalData('strava_pr_count', $this->getIntegerData($activityData, 'pr_count'))
                ->setAdditionalData('strava_achievement_count', $this->getIntegerData($activityData, 'achievement_count'));
            $updated[] = 'details';
        }

        $stats = $this->fillStats($activityData, $existingActivity->statsFrom('strava')->first() ?? new Stats(['stats_id' => $existingActivity->id, 'stats_type' => $existingActivity::class]));
        if (
            collect($stats->toArray())
                ->filter(fn ($value, $key) => array_key_exists($key, $activityData) && $activityData[$key] !== $value)
                ->count() > 0
        ) {
            $updated[] = 'stats';
        }
        $stats->save();

        $existingActivity = $importer->save();

        LoadStravaActivity::dispatch($existingActivity);
        LoadStravaStats::dispatch($existingActivity);

        if ($this->getIntegerData($activityData, 'comment_count') > 0) {
            LoadStravaComments::dispatch($existingActivity);
        }
        if ($this->getIntegerData($activityData, 'kudos_count') > 0) {
            LoadStravaKudos::dispatch($existingActivity);
        }
        if ($this->getIntegerData($activityData, 'total_photo_count') > 0) {
            LoadStravaPhotos::dispatch($existingActivity);
        }

        $jobs = [
            'photos' => LoadStravaPhotos::class,
            'comments' => LoadStravaComments::class,
            'kudos' => LoadStravaKudos::class,
            'details' => LoadStravaActivity::class,
            'stats' => LoadStravaStats::class,
        ];
        foreach ($updated as $updatedProperty) {
            if (array_key_exists($updatedProperty, $jobs)) {
                $jobs[$updatedProperty]::dispatch($existingActivity);
            }
        }
        if (count($updated) > 0) {
            $this->status = self::UPDATED;
        }

        return $existingActivity;
    }

    private function fillStats(array $activityData, Stats $stats): Stats
    {
        $stats->fill([
            'integration' => 'strava',
            'distance' => $activityData['distance'] ?? $stats->distance,
            'started_at' => isset($activityData['start_date']) ? Carbon::make($activityData['start_date']) : $stats->started_at,
            'duration' => $activityData['elapsed_time'] ?? $stats->duration,
            'average_speed' => $activityData['average_speed'] ?? $stats->average_speed,
            'min_altitude' => $activityData['elev_low'] ?? $stats->min_altitude,
            'max_altitude' => $activityData['elev_high'] ?? $stats->max_altitude,
            'elevation_gain' => $activityData['total_elevation_gain'] ?? $stats->elevation_gain,
            'moving_time' => $activityData['moving_time'] ?? $stats->moving_time,
            'max_speed' => $activityData['max_speed'] ?? $stats->max_speed,
            'average_cadence' => $activityData['average_cadence'] ?? $stats->average_cadence,
            'average_temp' => $activityData['average_temp'] ?? $stats->average_temp,
            'average_watts' => $activityData['average_watts'] ?? $stats->average_watts,
            'kilojoules' => $activityData['kilojoules'] ?? $stats->kilojoules,
            'start_latitude' => Arr::first($activityData['start_latlng'] ?? [$stats->start_latitude]),
            'start_longitude' => Arr::last($activityData['start_latlng'] ?? [$stats->start_longitude]),
            'end_latitude' => Arr::first($activityData['end_latlng'] ?? [$stats->end_latitude]),
            'end_longitude' => Arr::last($activityData['end_latlng'] ?? [$stats->end_longitude]),
        ]);

        return $stats;
    }

    public function status(): ?string
    {
        return $this->status;
    }
}
