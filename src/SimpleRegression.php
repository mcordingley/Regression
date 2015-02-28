<?php

namespace mcordingley\Regression;

use mcordingley\Regression\RegressionStrategy\LinearLeastSquares;

/**
 * SimpleRegression
 * 
 * A facade (in the GoF sense) over the other regression classes, for when you
 * just want to run a linear regression and get data out with a minimum of
 * digging through the documentation. Includes special handling of the intercept
 * apart from the other predictors.
 */
class SimpleRegression
{
    protected $regression;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->regression = new Regression(new LinearLeastSquares);
    }
    
    /**
     * addData
     * 
     * @param float $dependent The variable explained by $independentSeries.
     * @param array $independentSeries Array of explanatory variables.
     * @return self
     */
    public function addData($dependent, array $independentSeries)
    {
        $this->regression->addData($dependent, array_merge([1], $independentSeries));
        
        return $this;
    }
    
    /**
     * getCoefficients
     * 
     * @return array
     */
    public function getCoefficients()
    {
        return array_slice($this->regression->getCoefficients(), 1);
    }
    
    /**
     * getIntercept
     * 
     * @return float
     */
    public function getIntercept()
    {
        return $this->regression->getCoefficients()[0];
    }
    
    /**
     * predict
     * 
     * @param array $data
     * @return float
     */
    public function predict(array $data)
    {
        return $this->regression->predict([1] + $data);
    }
}