<?php

namespace mcordingley\Regression;

use InvalidArgumentException;
use LengthException;
use mcordingley\Regression\Linking\Identity;
use mcordingley\Regression\RegressionStrategy\LinearLeastSquares;

class Regression
{
    /**
     * dependentSeries
     * 
     * Array of floats representing the observed outcomes.
     * 
     * @var array
     */
    protected $dependentSeries = [];
    
    /**
     * independentSeries
     * 
     * Array of arrays of floats. Each sub-array is a set of explanatory
     * variables for one of the observed outcomes.
     * 
     * @var array
     */
    protected $independentSeries = [];
    
    /**
     * linking
     * 
     * Strategy object to transform Y values into and out of linear form.
     * 
     * @var Linking 
     */
    protected $linking;
    
    /**
     * predictors
     * 
     * The calculated beta values that show what the contribution of each
     * explanatory variable is to the overall fitted curve.
     * 
     * @var array
     */
    protected $predictors;
    
    /**
     * r2
     * 
     * Value in the range [0, 1] that shows how well the fitted curve fits the
     * data.
     * 
     * @var float
     */
    protected $r2;
    
    /**
     * strategy
     * 
     * Class instance that performs the actual regression.
     * 
     * @var RegressionStrategy 
     */
    protected $strategy;
    
    /**
     * __construct
     * 
     * @param RegressionStrategy A regression strategy to perform the calculations
     * @throws InvalidArgumentException
     */
    public function __construct(RegressionStrategy $regressionStrategy = null, Linking $linkingStrategy = null)
    {
        $this->strategy = $regressionStrategy ?: new LinearLeastSquares;
        $this->linking = $linkingStrategy ?: new Identity;
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
        
        $this->predictors = null;
        $this->r2 = null;
        
        return $this;
    }
    
    /**
     * calculateRSquared
     * 
     * Calculates the goodness of fit for the model, setting $this->r2 when done.
     */
    protected function calculateRSquared()
    {   
        $mean = array_reduce($this->dependentSeries, function ($memo, $value) {
            return $memo + $value;
        }) / count($this->dependentSeries);
        
        $sumSquaredError = 0;
        $meanSquaredError = 0;
        
        foreach ($this->independentSeries as $index => $series) {
            $actual = $this->dependentSeries[$index];
            $predicted = $this->predict($series);
            
            $sumSquaredError += pow($actual - $predicted, 2);
            $meanSquaredError += pow($actual - $mean, 2);
        }
        
        $this->r2 = 1 - $sumSquaredError / $meanSquaredError;
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
     * getCoefficents
     * 
     * Returns the coefficients determined by the regression.
     * 
     * @return array
     */
    public function getCoefficients()
    {
        if (is_null($this->predictors)) {
            $this->regress();
        }
        
        return $this->predictors;
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
        if (is_null($this->r2)) {
            $this->calculateRSquared();
        }
        
        return $this->r2;
    }

    /**
     * predict
     * 
     * @param array $series Data with which to make a prediction.
     * @return float The predicted value.
     */
    public function predict(array $series)
    {   
        $products = array_map(function ($predictor, $datum) {
            return $predictor * $datum;
        }, $this->getCoefficients(), $series);
        
        $sumProduct = array_reduce($products, function($memo, $product) {
            return $memo + $product;
        }, 0);
        
        return $this->linking->delinearize($sumProduct);
    }

    /**
     * regress
     * 
     * Performs the regression, setting the predictors array to the result of
     * the regression.
     */
    protected function regress()
    {
        $this->checkData();
        
        $linearDependents = array_map([$this->linking, 'linearize'], $this->dependentSeries);
        
        $this->predictors = $this->strategy->regress($linearDependents, $this->independentSeries);
    }
}