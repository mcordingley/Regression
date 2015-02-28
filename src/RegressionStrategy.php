<?php

namespace mcordingley\Regression;

interface RegressionStrategy
{
    /**
     * regress
     * 
     * @param array $independentData Array of arrays of explanatory variables
     * @param array $dependentData Array of values explained by $independentData
     * @return array Single array of predictor coefficients
     */
    public function regress(array $independentData, array $dependentData);
}