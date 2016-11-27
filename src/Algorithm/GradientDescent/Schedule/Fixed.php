<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * Simple step rule that always provides a fixed step size to the descent.
 * Since the gradient of the error becomes shallower as the descent nears
 * convergence, this will naturally shrink the updates into the error
 * function's minimum. However, too large of a step size will lead to the
 * descent diverging and too small of a step size will lead to an extremely
 * long descent. Unfortunately, choosing a good step size is a matter of
 * trial and error.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\Schedule
 */
final class Fixed implements Schedule
{
    /** @var float */
    private $stepSize;

    /**
     * @param float $stepSize
     */
    public function __construct(float $stepSize = 0.01)
    {
        $this->stepSize = $stepSize;
    }

    /**
     * @param array $gradient
     */
    public function update(array $gradient)
    {
        //
    }

    /**
     * @param int $featureIndex
     * @return float
     */
    public function step(int $featureIndex): float
    {
        return $this->stepSize;
    }
}
