<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

final class GradientNorm implements StoppingCriteria
{
    /** @var float */
    private $eta;

    /**
     * @param float $eta
     */
    public function __construct($eta)
    {
        $this->eta = $eta;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients)
    {
        return sqrt(array_sum(array_map(function ($slope) {
            return pow($slope, 2);
        }, $gradient))) <= $this->eta;
    }
}
