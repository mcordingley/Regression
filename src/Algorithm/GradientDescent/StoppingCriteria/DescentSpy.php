<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

/**
 * StoppingCriteria decorator that will call some callback on each iteration
 * before passing on to the delegated criteria. Useful for debugging gradient
 * descent convergence.
 *
 * @package mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
final class DescentSpy implements StoppingCriteria
{
    /** @var StoppingCriteria */
    private $criteria;

    /** @var callable */
    private $onIteration;

    /**
     * @param StoppingCriteria $criteria
     * @param callable $onIteration
     */
    public function __construct(StoppingCriteria $criteria, callable $onIteration)
    {
        $this->criteria = $criteria;
        $this->onIteration = $onIteration;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients)
    {
        call_user_func_array($this->onIteration, func_get_args());

        return $this->criteria->converged($gradient, $coefficients);
    }
}
