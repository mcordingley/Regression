<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent;

use MCordingley\Regression\Algorithm\Algorithm;
use MCordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use MCordingley\Regression\Data\Collection;

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
     * @param Collection $observations
     * @return array
     */
    final public function regress(Collection $observations): array
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
     * @param Collection $observations
     * @param array $coefficients
     * @return array
     */
    abstract protected function calculateGradient(Collection $observations, array $coefficients): array;

    /**
     * @param array $coefficients
     * @param array $gradient
     * @return array
     */
    private function updateCoefficients(array $coefficients, array $gradient): array
    {
        foreach ($gradient as $i => $slope) {
            $coefficients[$i] -= $this->schedule->step($i) * $slope;
        }

        return $coefficients;
    }
}
