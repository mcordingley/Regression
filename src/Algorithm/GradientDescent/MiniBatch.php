<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use mcordingley\Regression\Observations;

final class MiniBatch extends GradientDescent
{
    /** @var int */
    private $batchSize;

    /**
     * @param Gradient $gradient
     * @param Schedule $schedule
     * @param StoppingCriteria $stoppingCriteria
     * @param int $batchSize
     */
    public function __construct(Gradient $gradient, Schedule $schedule, StoppingCriteria $stoppingCriteria, $batchSize)
    {
        parent::__construct($gradient, $schedule, $stoppingCriteria);

        $this->batchSize = $batchSize;
    }

    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Observations $observations, array $coefficients)
    {
        $gradient = array_fill(0, count($observations->getObservation(0)->getFeatures()), 0.0);
        $batchElementIndices = (array) array_rand(range(0, count($observations) - 1), $this->batchSize);

        foreach ($batchElementIndices as $index) {
            $observation = $observations->getObservation($index);
            $observationGradient = $this->gradient->gradient($coefficients, $observation->getFeatures(), $observation->getOutcome());

            foreach ($observationGradient as $i => $observationSlope) {
                $gradient[$i] += $observationSlope / $this->batchSize;
            }
        }

        return $gradient;
    }
}
