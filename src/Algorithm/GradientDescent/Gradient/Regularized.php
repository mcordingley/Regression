<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Gradient;

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
     * @return self
     */
    public function ignoreFirstFeature(bool $ignoreFirst = true): self
    {
        $this->ignoreFirst = $ignoreFirst;

        return $this;
    }

    /**
     * Sets the regularization cost parameter. Default value is 1.0
     *
     * @param float $lambda
     * @return self
     */
    public function setLambda(float $lambda): self
    {
        $this->lambda = $lambda;

        return $this;
    }

    /**
     * Sets regularization level. e.g. L1 and L2. The default value is 2.
     *
     * @param int $level
     * @return self
     */
    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return float
     */
    public function cost(array $coefficients, array $features, float $outcome): float
    {
        $penalty = 0.0;

        foreach ($coefficients as $i => $coefficient) {
            if ($i || !$this->ignoreFirst) {
                $penalty += pow(abs($coefficient), $this->level);
            }
        }

        return $this->gradient->cost($coefficients, $features, $outcome) + $this->lambda * $penalty / $this->level;
    }

    /**
     * @param array $coefficients
     * @param array $features
     * @param float $outcome
     * @return array
     */
    public function gradient(array $coefficients, array $features, float $outcome): array
    {
        $gradient = [];
        $baseGradient = $this->gradient->gradient($coefficients, $features, $outcome);

        for ($i = 0; $i < count($baseGradient); $i++) {
            $penalty = ($i || !$this->ignoreFirst) ? $this->lambda * pow(abs($coefficients[$i]), $this->level - 1) : 0.0;
            $gradient[] = $baseGradient[$i] + $penalty;
        }

        return $gradient;
    }
}
