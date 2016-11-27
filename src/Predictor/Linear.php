<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Predictor;

final class Linear implements Predictor
{
    /** @var array */
    private $coefficients;

    /**
     * @param array $coefficients
     */
    public function __construct(array $coefficients)
    {
        $this->coefficients = $coefficients;
    }

    /**
     * @param array $features
     * @return float
     */
    public function predict(array $features): float
    {
        return (float) array_sum(array_map(function ($coefficient, $feature) {
            return $coefficient * $feature;
        }, $this->coefficients, $features));
    }
}
