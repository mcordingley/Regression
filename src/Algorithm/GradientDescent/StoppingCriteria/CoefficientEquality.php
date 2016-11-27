<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

/**
 * Checks for when the coefficients themselves have completely stopped changing.
 * This represents a complete convergence of the descent, but can take a long
 * time to occur, as the update for each weight must become smaller than can be
 * represented in floating points.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
final class CoefficientEquality implements StoppingCriteria
{
    /** @var array */
    private $oldCoefficients;

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients): bool
    {
        $oldCoefficients = $this->oldCoefficients;
        $this->oldCoefficients = $coefficients;

        return $oldCoefficients === $coefficients;
    }
}
