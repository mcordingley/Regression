<?php

declare(strict_types=1);

namespace mcordingley\Regression\RegressionAlgorithms;

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
     * @param DataBag $data
     * @return CoefficientSet
     */
    public function regress(DataBag $data): CoefficientSet;
}
