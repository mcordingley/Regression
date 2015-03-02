<?php

namespace mcordingley\Regression;

/**
 * RegressionStrategy
 * 
 * Strategy object used by the `Regression` class to find a set of predictor
 * coefficients. Encapsulates the algorithm used to calculate these values.
 */
interface RegressionStrategy
{
    /**
     * regress
     * 
     * @param array $dependentData Array of values explained by $independentData
     * @param array $independentData Array of arrays of explanatory variables
     * @return array Single array of predictor coefficients
     */
    public function regress(array $dependentData, array $independentData);
}