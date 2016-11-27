<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Predictor;

interface Predictor
{
    /**
     * @param array $features
     * @return float
     */
    public function predict(array $features): float;
}
