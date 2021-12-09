<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class MaxSpeed extends AnalyserContract implements PointAnalyser
{
    private ?float $maxSpeed = null;

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        return $analysis->setMaxSpeed($this->maxSpeed);
    }

    public function processPoint(Point $point): void
    {
        if($this->maxSpeed === null || $this->maxSpeed < $point->getSpeed()) {
            $this->maxSpeed = $point->getSpeed();
        }
    }

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getMaxSpeed() === null;
    }
}
