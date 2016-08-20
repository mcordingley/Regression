<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * Essentially takes a moving average of the squares of the gradient and uses
 * that to calculate step sizes. As with Adagrad, steeper slopes lead to smaller
 * step sizes while shallower slopes lead to larger step sizes. Unlike Adagrad,
 * step sizes are not necessarily strictly decreasing.
 *
 * @package mcordingley\Regression\Algorithm\GradientDescent\Schedule
 */
class RmsProp implements Schedule
{
    /** @var float */
    private $eta;

    /** @var array */
    private $history = [];

    /** @var float */
    private $rate;

    /** @var float */
    private $stepSize;

    /**
     * @param float $rate
     * @param float $stepSize
     * @param float $eta
     */
    public function __construct($rate = 0.9, $stepSize = 0.01, $eta = 0.000001)
    {
        $this->stepSize = $stepSize;
        $this->rate = $rate;
        $this->eta = $eta;
    }

    /**
     * @param array $gradient
     */
    public function update(array $gradient)
    {
        foreach ($gradient as $i => $slope) {
            $history = isset($this->history[$i]) ? $this->history[$i] : pow($slope, 2);
            $this->history[$i] = $history * $this->rate + (1.0  - $this->rate) * pow($slope, 2);
        }
    }

    /**
     * @param int $featureIndex
     * @return float
     */
    public function step($featureIndex)
    {
        return $this->stepSize / sqrt($this->eta + $this->history[$featureIndex]);
    }
}