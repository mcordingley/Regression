<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent;

use MCordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use MCordingley\Regression\Data\Collection;

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
    public function __construct(Gradient $gradient, Schedule $schedule, StoppingCriteria $stoppingCriteria, int $batchSize)
    {
        parent::__construct($gradient, $schedule, $stoppingCriteria);

        $this->batchSize = $batchSize;
    }

    /**
     * @param Collection $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Collection $observations, array $coefficients): array
    {
        $gradient = array_fill(0, $observations->getFeatureCount(), 0.0);
        $batchElementIndices = (array) array_rand(range(0, $observations->count() - 1), $this->batchSize);

        foreach ($batchElementIndices as $index) {
            $observation = $observations->getObservation($index);

            $observationGradient = $this->gradient->gradient(
                $coefficients,
                $observation->getFeatures(),
                $observation->getOutcome()
            );

            foreach ($observationGradient as $i => $observationSlope) {
                $gradient[$i] += $observationSlope / $this->batchSize;
            }
        }

        return $gradient;
    }
}
