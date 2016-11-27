<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

/**
 * Checks for when any of a given list of stopping criteria is met.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
final class Any implements StoppingCriteria
{
    /** @var array */
    private $criteriaList = [];

    /**
     * @param array $criteriaList Array of StoppingCriteria instances
     */
    public function __construct(array $criteriaList = [])
    {
        foreach ($criteriaList as $criteria) {
            $this->addCriteria($criteria);
        }
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients): bool
    {
        /** @var StoppingCriteria $criteria */
        foreach ($this->criteriaList as $criteria) {
            if ($criteria->converged($gradient, $coefficients)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param StoppingCriteria $criteria
     */
    public function addCriteria(StoppingCriteria $criteria)
    {
        $this->criteriaList[] = $criteria;
    }
}
