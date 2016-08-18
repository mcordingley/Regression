<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

final class MaxTime implements StoppingCriteria
{
    /** @var int */
    private $seconds;

    /** @var int */
    private $startTime;

    /**
     * @param int $seconds
     */
    public function __construct($seconds)
    {
        $this->seconds = $seconds;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients)
    {
        $time = time();

        if (!$this->startTime) {
            $this->startTime = $time;
        }

        $elapsed = $time - $this->startTime;

        return $elapsed >= $this->seconds;
    }
}
