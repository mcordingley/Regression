<?php

namespace mcordingley\Regression;

use mcordingley\Regression\RegressionStrategy\LinearLeastSquares;

/**
 * SimpleRegression
 * 
 * A facade (in the GoF sense) over the other regression classes, for when you
 * just want to run a linear regression and get data out with a minimum of
 * digging through the documentation.
 */
class SimpleRegression
{
    protected $regression;

    /**
     * __construct
     * 
     * @param array $independentData
     * @param array $dependentData
     */
    public function __construct(array $independentData, array $dependentData)
    {
        $this->regression = new Regression(new LinearLeastSquares);
        
        foreach ($independentData as $series) {
            $this->regression->addIndependentSeries(new DataSeries([1] + $series));
        }
        
        $this->regression->setDependentSeries(new DataSeries([1] + $dependentData));
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
        return $this->regression->predict(new DataSeries([1] + $data));
    }
}