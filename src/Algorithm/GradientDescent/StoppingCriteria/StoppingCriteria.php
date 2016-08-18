<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

interface StoppingCriteria
{
    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients);
}