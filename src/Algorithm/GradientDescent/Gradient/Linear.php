<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Gradient;

final class Linear implements Gradient
{
    /** @var int */
    private $power;

    /**
     * @param int $power
     */
    public function __construct(int $power = 2)
    {
        $this->power = $power;
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $features, float $outcome): float
    {
        return (float) pow(abs($this->predicted($coefficients, $features) - $outcome), $this->power);
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @return float
     */
    private function predicted(array $coefficients, array $features): float
    {
        return (float) array_sum(array_map(function ($coefficient, $feature) {
            return $coefficient * $feature;
        }, $coefficients, $features));
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $features, float $outcome): array
    {
        $error = $this->predicted($coefficients, $features) - $outcome;
        $errorSign = $error < 0 ? -1 : 1;
        $iterationConstant = $errorSign * $this->power * pow(abs($error), $this->power - 1);

        return array_map(function ($feature) use ($iterationConstant) {
            return $iterationConstant * $feature;
        }, $features);
    }
}
