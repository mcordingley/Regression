<?php

namespace mcordingley\Regression\GradientDescent\Gradient;

final class Linear implements Gradient
{
    /** @var int */
    private $power;

    /**
     * @param int $power
     */
    public function __construct($power = 2)
    {
        $this->power = $power;
    }

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $observation, $outcome)
    {
        return abs(pow($this->predicted($coefficients, $observation) - $outcome, $this->power));
    }

    /**
     * @param array $coefficients
     * @param array $observation
     * @return float
     */
    private function predicted(array $coefficients, array $observation)
    {
        return array_sum(array_map(function ($coefficient, $feature) {
            return $coefficient * $feature;
        }, $coefficients, $observation));
    }

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $observation, $outcome)
    {
        $gradient = [];
        $predicted = $this->predicted($coefficients, $observation);

        for ($i = 0; $i < count($observation); $i++) {
            $gradient[] = $this->power * pow($predicted, $this->power - 1) * $observation[$i];
        }

        return $gradient;
    }
}
