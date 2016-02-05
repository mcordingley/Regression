<?php

declare(strict_types=1);

namespace mcordingley\Regression\RegressionAlgorithm;

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

        // Starting guess is that everything contributes equally.
        $oldCoefficients = null;
        $coefficients = array_fill(0, $explanatoryCount, 1.0);

        for ($iteration = 0; $coefficients !== $oldCoefficients; $iteration++) {
            $stepSize = 1000.0 / (1000.0 + $iteration);
            $observationIndex = $iteration % $observationCount;
            $oldCoefficients = $coefficients;

            for ($i = 0; $i < $explanatoryCount; $i++) {
                $coefficients[$j] += $stepSize * $this->gradient->loss($coefficients, $independentData[$observationIndex], $dependentData[$observationIndex], $i);
            }
        }

        return $coefficients;
    }
}
