<?php

namespace mcordingley\Regression\GradientDescent\Gradient;

interface Gradient
{
    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @param int $featureIndex
     * @return float
     */
    public function cost(array $coefficients, array $observation, $outcome, $featureIndex);

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @param int $featureIndex
     * @return float
     */
    public function gradient(array $coefficients, array $observation, $outcome, $featureIndex);
}
