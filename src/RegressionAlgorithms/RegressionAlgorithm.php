<?php

declare(strict_types=1);

namespace mcordingley\Regression\RegressionAlgorithms;

use mcordingley\Regression\Observations;

/**
 * RegressionAlgorithmInterface
 *
 * Strategy object used by the `Regression` class to find a set of predictor
 * coefficients. Encapsulates the algorithm used to calculate these values.
 */
interface RegressionAlgorithm
{
    /**
     * regress
     *
     * @param Observations $data
     * @return array
     */
    public function regress(Observations $data): array;
}
