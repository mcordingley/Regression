<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

interface StoppingCriteria
{
    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients): bool;
}
