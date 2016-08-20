<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

/**
 * Run the descent for a certain amount of time before stopping. Goes well with
 * some criteria that checks for convergence within an instance of `Any` to cap
 * the amount of time that a descent can run. Nice to make sure that a descent
 * doesn't become runaway on a production server.
 *
 * @package mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
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
