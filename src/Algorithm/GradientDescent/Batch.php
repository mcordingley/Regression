<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent;

use MCordingley\Regression\Data\Collection;
use MCordingley\Regression\Data\Entry;

final class Batch extends GradientDescent
{
    /**
     * @param Collection $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Collection $observations, array $coefficients): array
    {
        $gradient = array_fill(0, $observations->getFeatureCount(), 0.0);
        $batchSize = $observations->count();

        /** @var Entry $observation */
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
