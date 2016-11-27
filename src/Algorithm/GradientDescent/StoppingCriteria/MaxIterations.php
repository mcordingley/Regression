<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

/**
 * Place a cap on the total number of iterations through the descent. It won't give
 * actual convergence, but is good if "good enough" can be expected to be reached
 * after a given number of times through the data. Note that each iteration does
 * not necessarily correspond to a complete epoch of the data. Check your descent
 * method for how many records are processed per iteration. Pairs well with a
 * criteria that checks for actual convergence inside of an `Any` object to stop
 * early if convergence has occurred.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
final class MaxIterations implements StoppingCriteria
{
    /** @var int */
    private $iterations = 0;

    /** @var int */
    private $max;

    /**
     * @param int $max
     */
    public function __construct(int $max)
    {
        $this->max = $max;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients): bool
    {
        $this->iterations++;

        return $this->iterations >= $this->max;
    }
}
