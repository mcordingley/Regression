<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * Automatically tunes the step size of for each feature. Features with steep
 * slopes will get rapidly get smaller steps while features with shallow slopes
 * will get larger steps. Either way, the step size for each feature is strictly
 * decreasing over time.
 *
 * @link https://xcorr.net/2014/01/23/adagrad-eliminating-learning-rates-in-stochastic-gradient-descent/
 */
final class Adagrad implements Schedule
{
    /** @var float */
    private $eta;

    /** @var float */
    private $stepSize;

    /** @var array */
    private $sumSquaredGradient;

    /**
     * @param float $stepSize
     * @param float $eta
     */
    public function __construct($stepSize = 0.01, $eta = 0.000001)
    {
        $this->stepSize = $stepSize;
        $this->eta = $eta;
    }

    /**
     * @param array $gradient
     */
    public function update(array $gradient)
    {
        if (!$this->sumSquaredGradient) {
            $this->sumSquaredGradient = array_fill(0, count($gradient), 0.0);
        }

        foreach ($gradient as $index => $slope) {
            $this->sumSquaredGradient[$index] += pow($slope, 2);
        }
    }

    /**
     * @param int $featureIndex
     * @return float
     */
    public function step($featureIndex)
    {
        return $this->stepSize / ($this->eta + sqrt($this->sumSquaredGradient[$featureIndex]));
    }
}
