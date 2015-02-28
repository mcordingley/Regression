<?php

namespace mcordingley\Regression;

use InvalidArgumentException;
use LengthException;

class Regression
{
    protected $dependentSeries = [];
    protected $dirty = true;
    protected $independentSeries = [];
    protected $predictors;
    protected $strategy;
    
    /**
     * __construct
     * 
     * @param RegressionStrategy A regression strategy to perform the calculations
     * @throws InvalidArgumentException
     */
    public function __construct(RegressionStrategy $regressionStrategy)
    {
        $this->strategy = $regressionStrategy;
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
        $this->dependentSeries[] = $dependent;
        $this->independentSeries[] = $independentSeries;
        $this->dirty = true;
        
        return $this;
    }
    
    /**
     * checkData
     * 
     * Checks the data provided to the regression to make sure that it is valid
     * prior to attempting the regression.
     * 
     * @throws LengthException
     */
    protected function checkData()
    {
        if (!count($this->independentSeries)) {
            throw new LengthException('Cannot perform regression; no data provided.');
        }
        
        $length = count($this->independentSeries[0]);
        
        if (!$length) {
            throw new LengthException('Cannot perform regression; no data points in the first independent data series.');
        }
        
        for ($i = 1, $len = count($this->independentSeries); $i < $len; $i++) {
            if (count($this->independentSeries[$i]) != $length) {
                throw new LengthException('Cannot perform regression; every provided independent data series must be of the same length.');
            }
        }
    }
    
    /**
     * checkDirty
     * 
     * Checks to see if the data is dirty and runs the regression if so.
     */
    protected function checkDirty()
    {
        if ($this->dirty) {
            $this->regress();
        }
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
        $this->checkDirty();
        
        return $this->predictors;
    }

    /**
     * predict
     * 
     * @param array $series Data with which to make a prediction.
     * @return float The predicted value.
     */
    public function predict(array $series)
    {
        $this->checkDirty();
        
        $products = array_map(function ($predictor, $datum) {
            return $predictor * $datum;
        }, $this->predictors, $series);
        
        $sumProduct = array_reduce($products, function($memo, $product) {
            return $memo + $product;
        }, 0);
        
        return $sumProduct;
    }

    /**
     * regress
     * 
     * Performs the regression, setting the predictors array to the result of
     * the regression and clearing the dirty flag.
     */
    protected function regress()
    {
        $this->checkData();
        
        $this->predictors = $this->strategy->regress($this->dependentSeries, $this->independentSeries);
        
        $this->dirty = false;
    }
}