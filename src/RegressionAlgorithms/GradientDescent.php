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
    private $gradient;
    private $maxIterations = 1000000;

    /**
     * __construct
     *
     * @param Gradient $gradient
     */
    public function __construct(Gradient $gradient)
    {
        $this->gradient = $gradient;
    }

    public function regress(Observations $data): array
    {
        $dependentData = $data->getDependents();
        $independentData = $data->getIndependents();

        $observationCount = count($independentData);
        $explanatoryCount = count($independentData[0]);

        $oldCoefficients = null;
        $coefficients = array_fill(0, $explanatoryCount, 0.0);
        $coefficientStepSizes = array_fill(0, $explanatoryCount, 0.0);

        for ($iteration = 0; $coefficients !== $oldCoefficients && $iteration < $this->maxIterations; $iteration++) {
            $oldCoefficients = $coefficients;
            $gradient = array_fill(0, $explanatoryCount, 0.0);

            for ($i = 0; $i < $explanatoryCount; $i++) {
                for ($observationIndex = 0; $observationIndex < $observationCount; $observationIndex++) {
                    $gradient[$i] += $this->gradient->loss($oldCoefficients, $independentData[$observationIndex], $dependentData[$observationIndex], $i);
                }

                $coefficientStepSizes[$i] += pow($gradient[$i], 2.0);
                $stepSize = 0.01 / (0.000001 + sqrt($coefficientStepSizes[$i]));

                $coefficients[$i] -= $stepSize * $gradient[$i];
            }
        }

        return $coefficients;
    }

    /**
     * setMaxIterations
     *
     * Sets the maximum number of iterations through the training data that the
     * descent will make.
     *
     * @param int $maxIterations Set to 0 for unlimited iterations.
     */
    public function setMaxIterations(int $maxIterations)
    {
        $this->maxIterations = $maxIterations ?: INF;
    }
}
