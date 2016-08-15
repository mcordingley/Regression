<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Gradient;

class Logistic implements Gradient
{
    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $features, $outcome)
    {
        $predicted = $this->predicted($coefficients, $features);

        return -$outcome * log($predicted) - (1.0 - $outcome) * log(1.0 - $predicted);
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @return float
     */
    private function predicted(array $coefficients, array $features)
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
    public function gradient(array $coefficients, array $features, $outcome)
    {
        $gradient = [];
        $predicted = $this->predicted($coefficients, $features);

        for ($i = 0; $i < count($features); $i++) {
            $gradient[] = ($predicted - $outcome) * $features[$i];
        }

        return $gradient;
    }
}