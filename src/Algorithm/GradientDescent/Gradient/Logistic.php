<?php

namespace mcordingley\Regression\GradientDescent\Gradient;

class Logistic implements Gradient
{

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @param int $featureIndex
     * @return float
     */
    public function cost(array $coefficients, array $observation, $outcome, $featureIndex)
    {
        $predicted = $this->predicted($coefficients, $observation);

        return -$outcome * log($predicted) - (1 - $outcome) * log(1 - $predicted);
    }

    /**
     * @param array $coefficients
     * @param array $observation
     * @return float
     */
    private function predicted(array $coefficients, array $observation)
    {
        return 1.0 / (1.0 + exp(-array_sum(array_map(function ($coefficient, $feature) {
            return $coefficient * $feature;
        }, $coefficients, $observation))));
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
        return ($this->predicted($coefficients, $observation) - $outcome) * $observation[$featureIndex];
    }
}
