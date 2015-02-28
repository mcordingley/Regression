<?php

namespace mcordingley\Regression;

use mcordingley\Regression\DataSeries;

interface RegressionStrategy
{
    /**
     * regress
     * 
     * @param array $independentData Array of DataSeries objects of data
     * @param DataSeries $dependentData DataSeries object of data that is explained by $independentData
     * @return array Single array of predictor coefficients
     */
    public function regress(array $independentData, DataSeries $dependentData);
}