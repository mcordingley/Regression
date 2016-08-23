<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * @package mcordingley\Regression\Algorithm\GradientDescent\Schedule
 * @link http://sebastianruder.com/optimizing-gradient-descent/index.html#adam
 */
final class Adam implements Schedule
{
    /** @var float */
    private $eta;

    /** @var float */
    private $meanBeta;

    /** @var array */
    private $means;

    /** @var float */
    private $stepSize;

    /** @var float */
    private $varianceBeta;

    /** @var array */
    private $variances;

    /**
     * @param float $stepSize
     * @param float $eta
     * @param float $meanBeta
     * @param float $varianceBeta
     */
    public function __construct($stepSize = 0.01, $eta = 0.00000001, $meanBeta = 0.9, $varianceBeta = 0.999)
    {
        $this->stepSize = $stepSize;
        $this->eta = $eta;
        $this->meanBeta = $meanBeta;
        $this->varianceBeta = $varianceBeta;
    }

    /**
     * @param array $gradient
     */
    public function update(array $gradient)
    {
        if (!$this->means) {
            $this->means = array_fill(0, count($gradient), 0.0);
            $this->variances = array_fill(0, count($gradient), 0.0);
        }

        foreach ($gradient as $i => $slope) {
            $historyMean = isset($this->means[$i]) ? $this->means[$i] : $slope;
            $this->means[$i] = $historyMean * $this->meanBeta + (1.0  - $this->meanBeta) * $slope;

            $historyVariance = isset($this->variances[$i]) ? $this->variances[$i] : pow($slope, 2);
            $this->variances[$i] = $historyVariance * $this->varianceBeta + (1.0  - $this->varianceBeta) * pow($slope, 2);
        }
    }


    public function step($featureIndex)
    {
        $biasedMean = $this->means[$featureIndex] / (1.0 - $this->meanBeta);
        $biasedVariance = $this->variances[$featureIndex] / (1.0 - $this->varianceBeta);

        return $this->stepSize * $biasedMean / (sqrt($biasedVariance) + $this->eta);
    }
}