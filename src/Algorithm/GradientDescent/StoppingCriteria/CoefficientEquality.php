<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

final class CoefficientEquality implements StoppingCriteria
{
    private $oldCoefficients;

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients)
    {
        $oldCoefficients = $this->oldCoefficients;
        $this->oldCoefficients = $coefficients;

        return $oldCoefficients === $coefficients;
    }
}
