<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Observation;
use mcordingley\Regression\Observations;

final class Stochastic extends GradientDescent
{
    /** @var array */
    private $shuffled;

    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Observations $observations, array $coefficients)
    {
        $observation = $this->getObservation($observations);

        return $this->gradient->gradient($coefficients, $observation->getFeatures(), $observation->getOutcome());
    }

    /**
     * @param Observations $observations
     * @return Observation
     */
    private function getObservation(Observations $observations)
    {
        if (!$this->shuffled) {
            $this->shuffled = range(0, count($observations) - 1);
            shuffle($this->shuffled);
        }

        return $observations->getObservation(array_pop($this->shuffled));
    }
}
