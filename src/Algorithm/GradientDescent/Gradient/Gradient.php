<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Gradient;

interface Gradient
{
    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $features, float $outcome): float;

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $features, float $outcome): array;
}
