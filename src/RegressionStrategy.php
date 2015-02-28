<?php

namespace mcordingley\Regression;

interface RegressionStrategy
{
    /**
     * regress
     * 
     * @param array $independentData Array of arrays of data
     * @param array $dependentData Single array of data that is explained by $independentData
     * @return array Single array of predictor coefficients
     */
    public function regress(array $independentData, array $dependentData);
}