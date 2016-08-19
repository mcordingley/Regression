<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Gradient;

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
     * @param array $features
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $features, $outcome)
    {
        return pow(abs($this->predicted($coefficients, $features) - $outcome), $this->power);
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @return float
     */
    private function predicted(array $coefficients, array $features)
    {
        return array_sum(array_map(function ($coefficient, $feature) {
            return $coefficient * $feature;
        }, $coefficients, $features));
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $features, $outcome)
    {
        $gradient = [];
        $predicted = $this->predicted($coefficients, $features);

        foreach ($features as $feature) {
            $gradient[] = $this->power * pow(abs($predicted - $outcome), $this->power - 1) * $feature;
        }

        return $gradient;
    }
}
