<?php

namespace App\Services\ActivityData;

use App\Models\Activity;
use App\Services\ActivityData\Contracts\Parser;
use App\Services\ActivityData\Parsers\FitParser;
use App\Services\ActivityData\Parsers\GpxParser;
use App\Services\ActivityData\Parsers\TcxParser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityDataFactory implements Contracts\ActivityDataFactory
{

    private array $custom = [];

    public function analyse(Activity $activity): Analysis
    {
        if($activity->activityFile) {
            return $this->parser($activity->activityFile->extension)->analyse($activity);
        }
        throw new NotFoundHttpException(sprintf('Activity %u does not have a file associated with it', $activity->id));
    }

    public function parser(string $type): Parser
    {
        if(array_key_exists($type, $this->custom)) {
            return call_user_func($this->custom[$type]);
        }

        $method = sprintf('create%sParser', ucfirst($type));
        if(method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new \Exception(sprintf('Cannot parse files of type [%s]', $type));
    }

    public function registerCustomParser(string $type, \Closure $creator)
    {
        $this->custom[$type] = $creator;
    }

    public function createGpxParser()
    {
        return new GpxParser(new Analysis());
    }

    public function createFitParser()
    {
        return new FitParser(new Analysis());
    }

    public function createTcxParser()
    {
        return new TcxParser(new Analysis());
    }

}