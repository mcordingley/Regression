<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

final class MaxIterations implements StoppingCriteria
{
    /** @var int */
    private $iterations = 0;

    /** @var int */
    private $max;

    /**
     * @param int $max
     */
    public function __construct($max)
    {
        $this->max = $max;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients)
    {
        $this->iterations++;

        return $this->iterations >= $this->max;
    }
}