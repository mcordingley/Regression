<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use mcordingley\Regression\Observations;

abstract class Base implements Algorithm
{
    /** @var Gradient */
    protected $gradient;

    /** @var Schedule */
    protected $schedule;

    /**
     * @param Gradient $gradient
     * @param Schedule $schedule
     */
    public function __construct(Gradient $gradient, Schedule $schedule)
    {
        $this->gradient = $gradient;
        $this->schedule = $schedule;
    }

    /**
     * @param Observations $observations
     * @return array
     */
    final public function regress(Observations $observations)
    {
        $features = $observations->getFeatures();
        $featureCount = count($features[0]);

        $oldCoefficients = null;
        $coefficients = array_fill(0, $featureCount, 0.0);

        while ($coefficients !== $oldCoefficients) {
            $oldCoefficients = $coefficients;
            $gradient = $this->calculateGradient($observations, $coefficients);
            $coefficients = $this->updateCoefficients($coefficients, $gradient);
            $this->schedule->update($gradient);
        }

        return $coefficients;
    }

    /**
     * @param Observations $observations
     * @param array $coefficients
     * @return array
     */
    abstract protected function calculateGradient(Observations $observations, array $coefficients);

    /**
     * @param array $coefficients
     * @param array $gradient
     * @return array
     */
    final protected function updateCoefficients(array $coefficients, array $gradient)
    {
        foreach ($gradient as $i => $slope) {
            $coefficients[$i] -= $this->schedule->step($i) * $slope;
        }

        return $coefficients;
    }
}
