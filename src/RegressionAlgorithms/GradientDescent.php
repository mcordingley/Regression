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
        // Adagrad reference:
        // https://xcorr.net/2014/01/23/adagrad-eliminating-learning-rates-in-stochastic-gradient-descent/

        $dependentData = $data->getDependents();
        $independentData = $data->getIndependents();

        $observationCount = count($independentData);
        $explanatoryCount = count($independentData[0]);

        // Starting guess is that everything contributes equally.
        $oldCoefficients = null;
        $coefficients = array_fill(0, $explanatoryCount, 1.0);
        $coefficientStepSizes = array_fill(0, $explanatoryCount, 0.0);

        for ($iteration = 0; $coefficients !== $oldCoefficients; $iteration++) {
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
}
