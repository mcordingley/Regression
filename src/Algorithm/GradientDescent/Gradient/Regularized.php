<?php

namespace mcordingley\Regression\GradientDescent\Gradient;

final class Regularized implements Gradient
{
    /** @var Gradient */
    private $gradient;

    /** @var bool */
    private $ignoreFirst = false;

    /** @var float */
    private $lambda = 1.0;

    /** @var int */
    private $level = 2;

    /**
     * @param Gradient $gradient
     */
    public function __construct(Gradient $gradient)
    {
        $this->gradient = $gradient;
    }

    /**
     * Ignore the first feature when regularizing, as that is usually the bias (or intercept) term.
     *
     * @param boolean $ignoreFirst
     * @return Regularized
     */
    public function ignoreFirstFeature($ignoreFirst = true)
    {
        $this->ignoreFirst = $ignoreFirst;

        return $this;
    }

    /**
     * Sets the regularization cost parameter. Default value is 1.0
     *
     * @param float $lambda
     * @return Regularized
     */
    public function setLambda($lambda)
    {
        $this->lambda = $lambda;

        return $this;
    }

    /**
     * Sets regularization level. e.g. L1 and L2 Default value is 2.
     *
     * @param int $level
     * @return Regularized
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @param int $featureIndex
     * @return float
     */
    public function cost(array $coefficients, array $observation, $outcome, $featureIndex)
    {
        $penalty = pow(abs($coefficients[$featureIndex]), $this->level);

        return $this->gradient->cost($coefficients, $observation, $outcome, $featureIndex) + $this->lambda * $penalty / $this->level;
    }

    /**
     * @param array $coefficients
     * @param array $observation
     * @param float $outcome
     * @param int $featureIndex
     * @return float
     */
    public function gradient(array $coefficients, array $observation, $outcome, $featureIndex)
    {
        $penalty = $this->level * pow(abs($coefficients[$featureIndex]), $this->level - 1);

        return $this->gradient->gradient($coefficients, $observation, $outcome, $featureIndex) + $this->lambda * $penalty;
    }
}
