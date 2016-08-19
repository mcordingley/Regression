<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use mcordingley\Regression\Observations;

abstract class GradientDescent implements Algorithm
{
    /** @var Gradient */
    protected $gradient;

    /** @var Schedule */
    private $schedule;

    /** @var StoppingCriteria */
    private $stoppingCriteria;

    /**
     * @param Gradient $gradient
     * @param Schedule $schedule
     * @param StoppingCriteria $stoppingCriteria
     */
    public function __construct(Gradient $gradient, Schedule $schedule, StoppingCriteria $stoppingCriteria)
    {
        $this->gradient = $gradient;
        $this->schedule = $schedule;
        $this->stoppingCriteria = $stoppingCriteria;
    }

    /**
     * @param Observations $observations
     * @return array
     */
    final public function regress(Observations $observations)
    {
        $coefficients = array_fill(0, $observations->getFeatureCount(), 0.0);

        do {
            $gradient = $this->calculateGradient($observations, $coefficients);
            $this->schedule->update($gradient);
            $coefficients = $this->updateCoefficients($coefficients, $gradient);
        } while (!$this->stoppingCriteria->converged($gradient, $coefficients));

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
    private function updateCoefficients(array $coefficients, array $gradient)
    {
        foreach ($gradient as $i => $slope) {
            $coefficients[$i] -= $this->schedule->step($i) * $slope;
        }

        return $coefficients;
    }
}
