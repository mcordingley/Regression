<?php

namespace mcordingley\Regression;

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