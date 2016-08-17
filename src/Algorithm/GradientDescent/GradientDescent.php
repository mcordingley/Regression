<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use mcordingley\Regression\Observations;
use SplObjectStorage;

abstract class GradientDescent implements Algorithm
{
    /** @var Gradient */
    protected $gradient;

    /** @var SplObjectStorage */
    private $observers;

    /** @var Schedule */
    private $schedule;

    /**
     * @param Gradient $gradient
     * @param Schedule $schedule
     */
    public function __construct(Gradient $gradient, Schedule $schedule)
    {
        $this->gradient = $gradient;
        $this->schedule = $schedule;
        $this->observers = new SplObjectStorage;
    }

    /**
     * @param Observations $observations
     * @return array
     */
    final public function regress(Observations $observations)
    {
        $features = $observations->getFeatures();
        $featureCount = count($features[0]);

        $oldCoefficients = [];
        $coefficients = array_fill(0, $featureCount, 0.0);

        while (!$this->converged($coefficients, $oldCoefficients)) {
            $oldCoefficients = $coefficients;
            $gradient = $this->calculateGradient($observations, $coefficients);
            $coefficients = $this->updateCoefficients($coefficients, $gradient);

            $this->schedule->update($gradient);
            $this->notifyListeners($coefficients);
        }

        return $coefficients;
    }

    /**
     * @param array $coefficients
     * @param array $oldCoefficients
     * @return bool
     */
    protected function converged(array $coefficients, array $oldCoefficients)
    {
        return $coefficients === $oldCoefficients;
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

    /**
     * @param array $coefficients
     */
    private function notifyListeners(array $coefficients)
    {
        /** @var DescentIterationListener $observer */
        foreach ($this->observers as $observer) {
            $observer->onGradientDescentIteration($coefficients);
        }
    }

    /**
     * @param DescentIterationListener $listener
     */
    final public function addDescentIterationListener(DescentIterationListener $listener)
    {
        $this->observers->attach($listener);
    }
}
