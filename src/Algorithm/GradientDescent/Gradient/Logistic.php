<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Gradient;

final class Logistic implements Gradient
{
    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $features, float $outcome): float
    {
        $predicted = $this->predicted($coefficients, $features);

        return -$outcome * log($predicted) - (1.0 - $outcome) * log(1.0 - $predicted);
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @return float
     */
    private function predicted(array $coefficients, array $features): float
    {
        return 1.0 / (1.0 + exp(-array_sum(array_map(function ($coefficient, $feature) {
            return $coefficient * $feature;
        }, $coefficients, $features))));
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $features, float $outcome): array
    {
        $iterationConstant = $this->predicted($coefficients, $features) - $outcome;

        return array_map(function ($feature) use ($iterationConstant) {
            return $iterationConstant * $feature;
        }, $features);
    }
}
