<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use mcordingley\Regression\Observation;
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

        $oldCoefficients = null;
        $coefficients = array_fill(0, $featureCount, 0.0);

        while ($coefficients !== $oldCoefficients) {
            $oldCoefficients = $coefficients;
            $gradient = $this->calculateGradient($observations, $coefficients);
            $coefficients = $this->updateCoefficients($coefficients, $gradient);

            $this->schedule->update($gradient);

            if ($this->observers->count()) {
                $this->notifyListeners($coefficients, $this->calculateAverageCost($observations, $coefficients));
            }
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
    private function updateCoefficients(array $coefficients, array $gradient)
    {
        foreach ($gradient as $i => $slope) {
            $coefficients[$i] -= $this->schedule->step($i) * $slope;
        }

        return $coefficients;
    }

    /**
     * @param array $coefficients
     * @param float $cost
     */
    private function notifyListeners(array $coefficients, $cost)
    {
        /** @var DescentIterationListener $observer */
        foreach ($this->observers as $observer) {
            $observer->onGradientDescentIteration($coefficients, $cost);
        }
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

    /**
     * @param DescentIterationListener $listener
     */
    final public function addDescentIterationListener(DescentIterationListener $listener)
    {
        $this->observers->attach($listener);
    }
}
