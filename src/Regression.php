<?php

namespace mcordingley\Regression;

use InvalidArgumentException;
use LengthException;

class Regression
{
    protected $dependentDataSeries;
    protected $dirty = true;
    protected $independentDataSeries = [];
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
     * addIndependentSeries
     * 
     * Adds a new independent data series to the regression
     * 
     * @param DataSeries $series
     * @return self
     */
    public function addIndependentSeries(DataSeries $series)
    {
        $this->independentSeries[] = $series;
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
        if (!$this->dependentDataSeries) {
            throw new LengthException('Cannot perform regression; missing the dependent data series.');
        }
        
        if (!count($this->dependentDataSeries)) {
            throw new LengthException('Cannot perform regression; no data points in the dependent data series.');
        }
        
        if (!count($this->independentDataSeries)) {
            throw new LengthException('Cannot perform regression; no independent data series provided.');
        }
        
        $length = count($this->independentDataSeries[0]);
        
        if (!$length) {
            throw new LengthException('Cannot perform regression; no data points in the first independent data series.');
        }
        
        for ($i = 1, $len = count($this->independentDataSeries); $i < $len; $i++) {
            if (count($this->independentDataSeries[$i]) != $length) {
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
     * @param DataSeries $series Data with which to make a prediction.
     * @return float The predicted value.
     */
    public function predict(DataSeries $series)
    {
        $this->checkDirty();
        
        $products = array_map(function ($predictor, $datum) {
            return $predictor * $datum;
        }, $this->predictors, $series->getDesign());
        
        $sumProduct = array_reduce($products, function($memo, $product) {
            return $memo + $product;
        }, 0);
        
        return call_user_func($series->getInverse(), $sumProduct);
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
        
        $this->predictors = $this->strategy->regress($this->independentSeries, $this->dependentSeries);
        
        $this->dirty = false;
    }
    
    /**
     * setDependentSeries
     * 
     * Adds a new independent data series to the regression
     * 
     * @param DataSeries $series
     * @return self
     */
    public function setDependentSeries(DataSeries $series)
    {
        $this->dependentSeries = $series;
        $this->dirty = true;
        
        return $this;
    }
}