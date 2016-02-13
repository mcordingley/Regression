<?php

declare(strict_types=1);

namespace mcordingley\Regression\RegressionAlgorithms;

use mcordingley\Regression\Gradient;
use mcordingley\Regression\Observations;

/**
 * GradientDescent
 *
 * Implements a stochastic gradient descent on the data, cycling through each
 * data vector and nudging the weights in the direction of the latest datum with
 * decreasing step sizes.
 */
final class GradientDescent implements RegressionAlgorithm
{
    private $coefficientEpsilon = 0.000001;
    private $gradient;
    private $maxIterations = INF;

    /**
     * __construct
     *
     * @param Gradient $gradient
     */
    public function __construct(Gradient $gradient)
    {
        $this->gradient = $gradient;
    }

    /**
     * fuzzilyEquals
     *
     * @param array $first
     * @param array $second
     * @return bool
     */
    private function fuzzilyEquals(array $first, array $second): bool
    {
        for ($i = count($first); $i--; ) {
            if (abs(($first[$i] - $second[$i]) / ($first[$i] + $second[$i])) > $this->coefficientEpsilon) {
                return false;
            }
        }

        return true;
    }

    public function regress(Observations $data): array
    {
        // Adagrad reference:
        // https://xcorr.net/2014/01/23/adagrad-eliminating-learning-rates-in-stochastic-gradient-descent/

        $dependentData = $data->getDependents();
        $independentData = $data->getIndependents();

        $observationCount = count($independentData);
        $explanatoryCount = count($independentData[0]);

        $oldCoefficients = array_fill(0, $explanatoryCount, 5.0);
        $coefficients = array_fill(0, $explanatoryCount, 1.0);
        $coefficientStepSizes = array_fill(0, $explanatoryCount, 0.0);

        for ($iteration = 0; !$this->fuzzilyEquals($coefficients, $oldCoefficients) && $iteration < $this->maxIterations; $iteration++) {
            $observationIndex = $iteration % $observationCount;
            $oldCoefficients = $coefficients;

            for ($i = 0; $i < $explanatoryCount; $i++) {
                $gradient = $this->gradient->loss($oldCoefficients, $independentData[$observationIndex], $dependentData[$observationIndex], $i);
                $coefficientStepSizes[$i] += pow($gradient, 2.0);
                $stepSize = 0.01 / (0.000001 + sqrt($coefficientStepSizes[$i]));

                $coefficients[$i] -= $stepSize * $gradient;
            }
        }

        return $coefficients;
    }

    /**
     * setCoefficientEpsilon
     *
     * @param float $epsilon
     */
    public function setCoefficientEpsilon(float $epsilon)
    {
        $this->coefficientEpsilon = $epsilon;
    }

    /**
     * setMaxIterations
     *
     * @param int $maxIterations
     */
    public function setMaxIterations(int $maxIterations)
    {
        $this->maxIterations = $maxIterations;
    }
}
