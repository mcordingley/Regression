<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Schedule;

final class Fixed implements Schedule
{
    /** @var float */
    private $stepSize;

    /**
     * @param float $stepSize
     */
    public function __construct($stepSize = 0.01)
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
    public function step($featureIndex)
    {
        return $this->stepSize;
    }
}
