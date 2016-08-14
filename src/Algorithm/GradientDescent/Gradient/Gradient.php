<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Gradient;

interface Gradient
{
    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $observation, $outcome);

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $observation, $outcome);
}
