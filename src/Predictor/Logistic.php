<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Predictor;

final class Logistic implements Predictor
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
        return 1.0 / (1.0 + exp(-array_sum(array_map(function ($coefficient, $feature) {
                return $coefficient * $feature;
        }, $this->coefficients, $features))));
    }
}
