<?php

namespace App\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Events\StravaActivityCommentsUpdated;
use App\Integrations\Strava\Events\StravaActivityKudosUpdated;
use App\Integrations\Strava\Events\StravaActivityPhotosUpdated;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\Sync\Task;
use Carbon\Carbon;

class SaveNewActivities extends Task
{

    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->strava = $strava;
    }

    public function description(): string
    {
        return 'Save any new or updated Strava activities';
    }

    public function name(): string
    {
        return 'Import activities from Strava';
    }

    public function disableBecause(User $user): ?string
    {
        if(!$user->stravaTokens()->exists()) {
            return 'Your account must be connected to Strava';
        }
        return null;
    }

    public function run()
    {
        $client = $this->strava->setUserId($this->user()->id)->client();
        $page = 1;
        $analysisCount = 0;
        $newCount = 0;
        $updatedCount = 0;
        do {
            $this->line(sprintf('Collecting activities %u to %u', ($page - 1) * 50, $page * 50));
            $activities = $client->getActivities($page);
            $page = $page + 1;
            $analysisCount = $analysisCount + count($activities);
            foreach($activities as $activityData) {
                $matchingActivity = Activity::whereAdditionalData('strava_id', $activityData['id'])->first();

                if($matchingActivity === null) {
                    $this->createActivity($activityData);
                    $newCount++;
                } else {
                    $wasUpdated = false;

                    if ($matchingActivity->getAdditionalData('strava_photo_count') !== (int)$activityData['total_photo_count']) {
                        $wasUpdated = true;
                        $newActivity = ActivityImporter::update($matchingActivity)
                            ->setAdditionalData('strava_photo_count', (int)$activityData['total_photo_count'])
                            ->save();
                        if ((int)$activityData['total_photo_count'] > 0) {
                            StravaActivityPhotosUpdated::dispatch($newActivity);
                        }
                    }

                    if ($matchingActivity->getAdditionalData('strava_comment_count') !== (int)$activityData['comment_count']) {
                        $wasUpdated = true;
                        $newActivity = ActivityImporter::update($matchingActivity)
                            ->setAdditionalData('strava_comment_count', (int)$activityData['comment_count'])
                            ->save();
                        if ((int)$activityData['comment_count'] > 0) {
                            StravaActivityCommentsUpdated::dispatch($newActivity);
                        }
                    }

                    if ($matchingActivity->getAdditionalData('strava_kudos_count') !== (int)$activityData['kudos_count']) {
                        $wasUpdated = true;
                        $newActivity = ActivityImporter::update($matchingActivity)
                            ->setAdditionalData('strava_kudos_count', (int)$activityData['kudos_count'])
                            ->save();
                        if ((int)$activityData['kudos_count'] > 0) {
                            StravaActivityKudosUpdated::dispatch($newActivity);
                        }
                    }

                    if ($matchingActivity->getAdditionalData('strava_pr_count') !== (int)$activityData['pr_count']
                        || $matchingActivity->getAdditionalData('strava_achievement_count') !== (int)$activityData['achievement_count']) {
                        $wasUpdated = true;
                        $newActivity = ActivityImporter::update($matchingActivity)
                            ->setAdditionalData('strava_pr_count', (int)$activityData['pr_count'])
                            ->setAdditionalData('strava_achievement_count', (int)$activityData['achievement_count'])
                            ->save();
                        StravaActivityUpdated::dispatch($newActivity);
                    }

                    if ($wasUpdated) {
                        $updatedCount++;
                    }
                }
            }
            $this->offerBail(sprintf('Cancelled with %u new tasks added.', $newCount));
        } while(count($activities) > 0);

        $this->line(sprintf('Found %u activities, including %u new and %u updated.', $analysisCount, $newCount, $updatedCount));
    }

    private function createActivity(mixed $activityData): Activity
    {
        $activity = ActivityImporter::for($this->user())
            ->withName($activityData['name'])
            ->withDistance($activityData['distance'])
            ->linkTo('strava')
            ->startedAt(Carbon::make($activityData['start_date']))
            ->setAdditionalData('strava_id', (int) $activityData['id'])
            ->setAdditionalData('strava_upload_id', array_key_exists('upload_id_str', $activityData) ? (int) $activityData['upload_id_str'] : null)
            ->setAdditionalData('strava_photo_count', (int) $activityData['total_photo_count'] ?? null)
            ->setAdditionalData('strava_comment_count', (int) $activityData['comment_count'] ?? null)
            ->setAdditionalData('strava_kudos_count', (int) $activityData['kudos_count'] ?? null)
            ->setAdditionalData('strava_pr_count', (int) $activityData['pr_count'] ?? null)
            ->setAdditionalData('strava_achievement_count', (int) $activityData['achievement_count'] ?? null)
            ->import();

        StravaActivityUpdated::dispatch($activity);
        if((int) $activityData['comment_count'] > 0) {
            StravaActivityCommentsUpdated::dispatch($activity);
        }
        if((int) $activityData['kudos_count'] > 0) {
            StravaActivityKudosUpdated::dispatch($activity);
        }
        if((int) $activityData['total_photo_count'] > 0) {
            StravaActivityPhotosUpdated::dispatch($activity);
        }

        return $activity;
    }
}
