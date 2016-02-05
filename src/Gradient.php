<?php

declare(strict_types=1);

namespace mcordingley\Regression;

interface Gradient
{
    /**
     * loss
     *
     * The loss function to be used for gradient descent. Loosely speaking, this
     * function returns the slope of the error at the coordinates in error space
     * defined by the coefficients for the coefficient at the specified index.
     *
     * In technical terms, this is the partial derivative of the error function
     * with respect to the coefficient at `$index`.
     *
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @param int $index
     * @return float
     */
    public function loss(array $coefficients, array $observation, float $outcome, int $index): float;
}
