<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Observations;

final class Stochastic extends GradientDescent
{
    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Observations $observations, array $coefficients)
    {
        $observation = $observations->getObservation(mt_rand(0, count($observations) - 1));

        return $this->gradient->gradient($coefficients, $observation->getFeatures(), $observation->getOutcome());
    }
}
