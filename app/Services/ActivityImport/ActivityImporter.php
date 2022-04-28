<?php

namespace App\Services\ActivityImport;

use App\Models\Activity;
use App\Models\AdditionalData;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityImporter
{
    private ?User $user = null;

    private Activity $existingActivity;

    private ActivityDetails $activityDetails;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? Auth::user();
        $this->activityDetails = new ActivityDetails();
    }

    public static function update(Activity $activity): ActivityImporter
    {
        $user = $activity->user;
        $instance = (new static($user))->setExistingActivity($activity);
        if ($activity->name) {
            $instance->withName($activity->name);
        }
        if ($activity->description) {
            $instance->withDescription($activity->description);
        }
        if ($activity->file) {
            $instance->withActivityFile($activity->file);
        }
        if ($activity->additionalData()->exists()) {
            $instance->activityDetails()->setAdditionalArrayData($activity->getAllArrayAdditionalData()->toArray());
            $instance->activityDetails()->setAdditionalData($activity->getAllNonArrayAdditionalData()->toArray());
        }
        if ($activity->files()->exists()) {
            $instance->activityDetails()->setMedia(
                $activity->files()->get()->all()
            );
        }
        if ($activity->linked_to) {
            $instance->activityDetails()->setLinkedTo($activity->linked_to);
        }

        return $instance;
    }

    public function activityDetails(): ActivityDetails
    {
        return $this->activityDetails;
    }

    public static function for(User $user): ActivityImporter
    {
        return new static($user);
    }

    public function setExistingActivity(Activity $activity): ActivityImporter
    {
        $this->existingActivity = $activity;

        return $this;
    }

    public function withName(?string $name = null): ActivityImporter
    {
        if ($name !== null) {
            $this->activityDetails->setName($name);
        }

        return $this;
    }

    public function withDescription(?string $description = null): ActivityImporter
    {
        if ($description !== null) {
            $this->activityDetails->setDescription($description);
        }

        return $this;
    }

    public function withActivityFile(File $file): ActivityImporter
    {
        $this->activityDetails->setActivityFile($file);

        return $this;
    }

    public function setAdditionalData(string $key, mixed $value): ActivityImporter
    {
        $this->activityDetails->setAdditionalDataKey($key, $value);

        return $this;
    }

    public function pushToAdditionalDataArray(string $key, mixed $value): ActivityImporter
    {
        $this->activityDetails->pushToAdditionalDataArrayKey($key, $value);

        return $this;
    }

    public function unsetAdditionalData(string $key): ActivityImporter
    {
        $this->activityDetails->unsetAdditionalData($key);

        return $this;
    }

    public function removeFromAdditionalDataArray(string $key, mixed $value): ActivityImporter
    {
        $additionalData = $this->activityDetails->getAdditionalArrayData();
        if (array_key_exists($key, $additionalData) && is_array($additionalData[$key]) && in_array($value, $additionalData[$key])) {
            unset($additionalData[$key][array_search($value, $additionalData[$key])]);
        }
        $this->activityDetails->setAdditionalArrayData($additionalData);

        return $this;
    }

    public function linkTo(string $integration): ActivityImporter
    {
        $this->activityDetails->addLinkedTo($integration);

        return $this;
    }

    public function import(): Activity
    {
        return $this->saveActivityModel(new Activity());
    }

    public function save(): Activity
    {
        return $this->saveActivityModel($this->existingActivity);
    }

    private function saveActivityModel(Activity $activity): Activity
    {
        $activity->fill([
            'name' => $this->activityDetails->getName(),
            'description' => $this->activityDetails->getDescription(),
            'linked_to' => $this->activityDetails->getLinkedTo(),
            'file_id' => $this->activityDetails->getActivityFile()?->id,
            'user_id' => $this->user?->id ?? Auth::id() ?? throw new \Exception('A user could not be found to run the import against')
        ]);
        $activity->save();
        $activity->files()->sync(collect($this->activityDetails->getMedia())->pluck('id'));

        $currentAdditionalData = $activity->getAllAdditionalData();
        $newAdditionalData = collect($this->activityDetails->getAdditionalData());
        $newAdditionalArrayData = collect($this->activityDetails->getAdditionalArrayData());
        $currentAdditionalDataModels = $activity->additionalData()->get();

        // Iterate through each of the new additional datas
        foreach ($newAdditionalData as $key => $value) {
            if (!$currentAdditionalData->has($key)) {
                $activity->additionalData()->create(['key' => $key, 'value' => $value]);
            }
        }

        // Iterate through each of the new additional array datas
        foreach ($newAdditionalArrayData as $key => $value) {
            foreach ($value as $dataElement) {
                if (!$currentAdditionalData->has($key) || (is_array($currentAdditionalData->get($key)) && !in_array($dataElement, $currentAdditionalData->get($key)))) {
                    $activity->additionalData()->create(['key' => $key, 'value' => $dataElement]);
                }
            }
        }

        // Iterate through each of the old datas and remove any that aren't in use any more
        foreach ($currentAdditionalDataModels->groupBy(fn (AdditionalData $d) => $d->key) as $key => $additionalDataModels) {
            if ($additionalDataModels->count() > 1) {
                foreach ($additionalDataModels as $additionalDataModel) {
                    if (!$newAdditionalArrayData->has($key) || !in_array($additionalDataModel->value, $newAdditionalArrayData->get($key))) {
                        $additionalDataModel->delete();
                    }
                }
            } elseif ($additionalDataModels->count() === 1) {
                if (!$newAdditionalData->has($key)) {
                    $additionalDataModels->first()->delete();
                }
            }
        }

        return $activity;
    }

    public function addMedia(array $files): ActivityImporter
    {
        $this->activityDetails->setMedia(array_merge(
            $this->activityDetails->getMedia(),
            $files
        ));

        return $this;
    }
}
