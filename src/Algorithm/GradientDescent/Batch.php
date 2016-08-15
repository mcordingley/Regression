<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Observation;
use mcordingley\Regression\Observations;

final class Batch extends GradientDescent
{
    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Observations $observations, array $coefficients)
    {
        $gradient = array_fill(0, count($observations->getObservation(0)->getFeatures()), 0.0);

        /** @var Observation $observation */
        foreach ($observations as $observation) {
            $observationGradient = $this->gradient->gradient($coefficients, $observation->getFeatures(), $observation->getOutcome());

            foreach ($observationGradient as $i => $observationSlope) {
                $gradient[$i] += $observationSlope / count($observations);
            }
        }

        return $gradient;
    }
}
