<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class AverageSpeed extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getDuration() !== null
            && $analysis->getDistance() !== null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $duration = $analysis->getDuration();
        $distance = $analysis->getDistance();
        $averageSpeed = $distance / $duration;

        return $analysis->setAverageSpeed(round($averageSpeed, 2));
    }
}
