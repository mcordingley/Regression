<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Schedule;

/**
 * Decorates another StoppingCriteria and passes it the gradient as updated by the provided
 * Schedule object. This way, you can test your stopping criteria not against the current
 * gradient, but against the gradient as it is for coefficient updates. Useful for
 * Stochastic gradient descent and MiniBatch gradient descent with small batches, where the
 * actual gradient never quite settles down, despite the descent itself converging.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria
 */
final class SteppedCriteria implements StoppingCriteria
{
    /** @var StoppingCriteria */
    private $criteria;

    /** @var Schedule */
    private $schedule;

    /**
     * @param StoppingCriteria $criteria
     * @param Schedule $schedule
     */
    public function __construct(StoppingCriteria $criteria, Schedule $schedule)
    {
        $this->criteria = $criteria;
        $this->schedule = $schedule;
    }

    /**
     * @param array $gradient
     * @param array $coefficients
     * @return bool
     */
    public function converged(array $gradient, array $coefficients): bool
    {
        $steppedGradient = [];

        foreach ($gradient as $i => $slope) {
            $steppedGradient[] = $this->schedule->step($i) * $slope;
        }

        return $this->criteria->converged($steppedGradient, $coefficients);
    }
}