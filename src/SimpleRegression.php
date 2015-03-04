<?php

namespace mcordingley\Regression;

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
        $this->regression = new Regression;
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
     * getCoefficents
     * 
     * Returns the coefficients determined by the regression.
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
     * getRSquared
     * 
     * Calculates the coefficient of determination. i.e. how well the line of
     * best fit describes the data.
     * 
     * @return float
     */
    public function getRSquared()
    {
        return $this->regression->getRSquared();
    }
    
    /**
     * getStandardError
     * 
     * Calculates the standard error of the regression. This is the average
     * distance of observed values from the regression line. It's conceptually
     * similar to the standard deviation.
     * 
     * @return float
     */
    public function getStandardError()
    {
        return $this->regression->getStandardError();
    }
    
    /**
     * getStandardErrorCoefficients
     * 
     * Calculates the standard error of each of the regression coefficients.
     * 
     * @return array
     */
    public function getStandardErrorCoefficients()
    {
        return array_slice($this->regression->getStandardErrorCoefficients(), 1);
    }
    
    /**
     * getTValues
     * 
     * Calculates the t test values of each of the regression coefficients.
     * 
     * @return array
     */
    public function getTValues()
    {
        return array_slice($this->regression->getTValues(), 1);
    }
    
    /**
     * predict
     * 
     * @param array $series Data with which to make a prediction.
     * @return float The predicted value.
     */
    public function predict(array $data)
    {
        return $this->regression->predict(array_merge([1], $data));
    }
}