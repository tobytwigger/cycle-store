<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class TimesAndDurations extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $startTime = collect($analysis->getPoints())->first()->getTime();
        $endTime = collect($analysis->getPoints())->last()->getTime();

        if ($analysis->getDuration() === null) {
            $analysis->setDuration($endTime->diffInSeconds($startTime));
        }
        if ($analysis->getStartedAt() === null) {
            $analysis->setStartedAt($startTime);
        }
        if ($analysis->getFinishedAt() === null) {
            $analysis->setFinishedAt($endTime);
        }
        return $analysis;
    }

    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1;
    }
}