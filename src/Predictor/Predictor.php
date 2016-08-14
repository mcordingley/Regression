<?php

namespace mcordingley\Regression\Predictor;

interface Predictor
{
    /**
     * @param array $features
     * @return float
     */
    public function predict(array $features);
}
