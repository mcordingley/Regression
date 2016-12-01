<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * Adam, or "Adaptive Moment Estimation", is another schedule that automatically tunes the step sizes for each
 * coefficient. It builds on the theoretical foundation of RmsProp and addresses some issues that RMSProp and
 * Adagrad have. This is the currently recommended implementation for adaptive gradients and should be safe to
 * use without manual tuning of the constructor parameters.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\Schedule
 * @link http://sebastianruder.com/optimizing-gradient-descent/index.html#adam
 */
final class Adam implements Schedule
{
    /** @var float */
    private $eta;

    /** @var array */
    private $gradient;

    /** @var int */
    private $iteration = 0;

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
    public function __construct(float $stepSize = 0.001, float $eta = 0.00000001, float $meanBeta = 0.9, float $varianceBeta = 0.999)
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
            $this->means[$i] = $this->meanBeta * $this->means[$i] + (1.0  - $this->meanBeta) * $slope;
            $this->variances[$i] = $this->varianceBeta * $this->variances[$i] + (1.0  - $this->varianceBeta) * pow($slope, 2);
        }

        $this->iteration++;
        $this->gradient = $gradient;
    }

    /**
     * @param int $featureIndex
     * @return float
     */
    public function step(int $featureIndex): float
    {
        $correctedMean = $this->means[$featureIndex] / (1.0 - pow($this->meanBeta, $this->iteration));
        $correctedVariance = $this->variances[$featureIndex] / (1.0 - pow($this->varianceBeta, $this->iteration));

        /*
         * Need to put the gradient in the denominator here to counter the one in GradientDescent, since Adam takes
         * the unusual approach of not having it at all in the coefficient update step.
         */
        return $this->gradient[$featureIndex]
            ? $this->stepSize * $correctedMean / ((sqrt($correctedVariance) + $this->eta) * $this->gradient[$featureIndex])
            : 0;
    }
}
