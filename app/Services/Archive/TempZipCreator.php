<?php

namespace App\Services\Archive;

use Alchemy\Zippy\Zippy;
use App\Models\File;
use App\Services\Archive\Parser\FileResource;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TempZipCreator extends \App\Services\Archive\Contracts\ZipCreator
{

    public function archive(): File
    {
        $rand = Str::random();

        $files = $this->getFilesToSave($rand);

        Storage::disk('temp')->put(sprintf('zip_creator/%s/archive/test.txt', $rand), 'Test');
        $fullPath = dirname(Storage::disk('temp')->path(sprintf('zip_creator/%s/archive/test.txt', $rand)));
        $archivePath = sprintf('%s/result.zip', $fullPath);

        Zippy::load()->create($archivePath, $files);

        return Upload::withContents(
            file_get_contents($archivePath),
            sprintf('archive_created_%s.zip', Carbon::now()->format('Y_m_d_H_m_s')),
            $this->user(),
            FileUploader::ARCHIVE
        );
    }

    private function getFilesToSave(string $rand): array
    {
        $metaPaths = [];
        foreach($this->results->getAllMetadata() as $file => $data) {
            $path = sprintf('zip_creator/%s/meta/%s.json', $rand, $file);
            Storage::disk('temp')->put($path, json_encode($data, JSON_PRETTY_PRINT));
            $metaPaths[$file] = Storage::disk('temp')->path($path);
        }

        return collect($this->results->getFiles())
            ->mapWithKeys(function(FileResource $fileResource) {
                return [$fileResource->getNewPath() => $fileResource->fullPath()];
            })
            ->merge(collect($metaPaths)
                ->mapWithKeys(function(string $path, string $file) {
                    return [sprintf('%s.json', $file) => $path];
                }))
            ->all();
    }
}