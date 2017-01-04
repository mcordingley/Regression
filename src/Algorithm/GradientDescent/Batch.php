<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent;

use MCordingley\Regression\Observation;
use MCordingley\Regression\Observations;

final class Batch extends GradientDescent
{
    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Observations $observations, array $coefficients): array
    {
        $gradient = array_fill(0, $observations->getFeatureCount(), 0.0);
        $batchSize = $observations->count();

        /** @var Observation $observation */
        foreach ($observations as $observation) {
            $observationGradient = $this->gradient->gradient(
                $coefficients,
                $observation->getFeatures(),
                $observation->getOutcome()
            );

            foreach ($observationGradient as $j => $observationSlope) {
                $gradient[$j] += $observationSlope / $batchSize;
            }
        }

        return $gradient;
    }
}
