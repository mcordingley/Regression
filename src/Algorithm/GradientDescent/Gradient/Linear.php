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
     * @param int $featureIndex
     * @return float
     */
    public function cost(array $coefficients, array $observation, $outcome, $featureIndex)
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
     * @param int $featureIndex
     * @return float
     */
    public function gradient(array $coefficients, array $observation, $outcome, $featureIndex)
    {
        $predicted = $this->predicted($coefficients, $observation);

        return $this->power * pow($predicted, $this->power - 1) * $observation[$featureIndex];
    }
}
