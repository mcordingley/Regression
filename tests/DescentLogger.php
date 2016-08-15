<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\DescentIterationListener;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Observation;
use mcordingley\Regression\Observations;

class DescentLogger implements DescentIterationListener
{
    /** @var Gradient */
    private $gradient;

    /** @var Observations */
    private $observations;

    /**
     * @param Gradient $gradient
     * @param Observations $observations
     */
    public function __construct(Gradient $gradient, Observations $observations)
    {
        $this->gradient = $gradient;
    }

    /**
     * @param array $coefficients
     */
    public function onGradientDescentIteration(array $coefficients)
    {
        var_dump([$coefficients, $this->calculateAverageCost($this->observations, $coefficients)]);
    }

    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return float
     */
    private function calculateAverageCost(Observations $observations, array $coefficients)
    {
        $cost = 0.0;

        /** @var Observation $observation */
        foreach ($observations as $observation) {
            $cost += $this->gradient->cost($coefficients, $observation->getFeatures(), $observation->getOutcome()) / count($observations);
        }

        return $cost;
    }
}