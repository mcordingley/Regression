<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

final class GradientNorm implements StoppingCriteria
{
    /** @var float */
    private $eta;

    /** @var int */
    private $pNorm;

    /**
     * @param float $eta
     * @param int $pNorm
     */
    public function __construct($eta = 6.103515625E-5, $pNorm = 2)
    {
        $this->eta = $eta;
        $this->pNorm = $pNorm;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients)
    {
        return pow(array_sum(array_map(function ($slope) {
            return pow($slope, $this->pNorm);
        }, $gradient)), 1 / $this->pNorm) <= $this->eta;
    }
}
