<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

/**
 * Decorator that calls the underlying StoppingCriteria only after every N
 * iterations. This is for when the stopping criteria is expensive to
 * calculate, such as one that involves computing the cost of the entire
 * data-set given the current set of weights.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
final class NthIteration implements StoppingCriteria
{
    /** @var int */
    private $iteration = 0;

    /** @var int */
    private $n;

    /** @var StoppingCriteria */
    private $criteria;

    /**
     * @param StoppingCriteria $criteria
     * @param int $n
     */
    public function __construct(StoppingCriteria $criteria, int $n)
    {
        $this->criteria = $criteria;
        $this->n = $n;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients): bool
    {
        $this->iteration++;
        $this->iteration %= $this->n;

        if ($this->iteration) {
            return false;
        }

        return $this->criteria->converged($gradient, $coefficients);
    }
}
