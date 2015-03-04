<?php

namespace mcordingley\Regression;

use InvalidArgumentException;
use LengthException;
use mcordingley\Regression\Linking\Identity;
use mcordingley\Regression\Linking\LinkingInterface;
use mcordingley\Regression\RegressionAlgorithm\LinearLeastSquares;
use mcordingley\Regression\RegressionAlgorithm\RegressionAlgorithmInterface;

/**
 * Regression
 * 
 * Represents a regression analysis. Each instance maps to a particular 
 * regression analysis.
 */
class Regression
{
    /**
     * algorithm
     * 
     * Class instance that performs the actual regression.
     * 
     * @var RegressionAlgorithmInterface 
     */
    protected $algorithm;
    
    /**
     * dependentLinking
     * 
     * Strategy object to transform Y values into and out of linear form.
     * 
     * @var LinkingInterface 
     */
    protected $dependentLinking;
    
    /**
     * dependentSeries
     * 
     * Array of floats representing the observed outcomes.
     * 
     * @var array
     */
    protected $dependentSeries = [];
    
    /**
     * independentLinking
     * 
     * Strategy object to use by default to linearize independent variables.
     * 
     * @var LinkingInterface
     */
    protected $independentLinking;
    
    /**
     * independentLinkings
     * 
     * Sparse array of strategy objects to linearize specific independent
     * variables by index.
     * 
     * @var array 
     */
    protected $independentLinkings = [];
    
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
     * S
     * 
     * The S statistic is also known as the Standard Error of the regression,
     * which is the average distance of observed values from the regression
     * line.
     * 
     * @var float 
     */
    protected $S;
    
    /**
     * __construct
     * 
     * @param RegressionStrategy A regression strategy to perform the calculations
     * @throws InvalidArgumentException
     */
    public function __construct(RegressionAlgorithmInterface $regressionStrategy = null)
    {
        // Set sane defaults for internal objects.
        $this->algorithm = $regressionStrategy ?: new LinearLeastSquares;
        
        $identity = new Identity;
        $this->dependentLinking = $identity;
        $this->independentLinking = $identity;
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
        $this->S = null;
        
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
            $this->calculateStatistics();
        }
        
        return $this->r2;
    }
    
    /**
     * @return typegetStandardError
     * 
     * Calculates the standard error of the regression. This is the average
     * distance of observed values from the regression line. It's conceptually
     * similar to the standard deviation.
     * 
     * @return float
     */
    public function getStandardError()
    {
        if (is_null($this->S)) {
            $this->calculateStatistics();
        }
        
        return $this->S;
    }

    /**
     * predict
     * 
     * @param array $series Data with which to make a prediction.
     * @return float The predicted value.
     */
    public function predict(array $series)
    {
        $transformed = [];
        
        foreach ($series as $index => $datum) {
            $transformed[] = isset($this->independentLinkings[$index]) ? $this->independentLinkings[$index]->linearize($datum) : $this->independentLinking->linearize($datum);
        }
        
        $products = array_map(function ($predictor, $datum) {
            return $predictor * $datum;
        }, $this->getCoefficients(), $transformed);
        
        $sumProduct = array_reduce($products, function($memo, $product) {
            return $memo + $product;
        }, 0);
        
        return $this->dependentLinking->delinearize($sumProduct);
    }

    /**
     * setDependentLinking
     * 
     * Sets the linking object used to transform dependent values into and out
     * of linear form for regression. Defaults to the identity function if this
     * method isn't used to change it.
     * 
     * @param LinkingInterface $linking
     * @return self
     */
    public function setDependentLinking(Linking $linking)
    {
        $this->dependentLinking = $linking;
        
        return $this;
    }
    
    /**
     * setIndependentLinking
     * 
     * Sets the linking object used to transform indepenent variables prior to
     * regression. If no index is supplied, this is used as the default transform
     * for all independent variables. If an index is supplied, then it is used only
     * for independent variables with a matching index.
     * 
     * @param LinkingInterface $linking
     * @param int|null $index If specified, use this linking only for independent variables at the specified index
     * @return self
     */
    public function setIndependentLinking(Linking $linking, $index = null)
    {
        if (is_null($index)) {
            $this->independentLinking = $linking;
        } else {
            $this->independentLinkings[$index] = $linking;
        }
        
        return $this;
    }
    
    /**
     * calculateStatistics
     * 
     * Calculates the goodness of fit for the model, setting $this->r2 when done.
     */
    protected function calculateStatistics()
    {
        $count = count($this->dependentSeries);
        
        $mean = array_reduce($this->dependentSeries, function ($memo, $value) {
            return $memo + $value;
        }) / $count;
        
        $sumSquaredError = 0;
        $meanSquaredError = 0;
        
        foreach ($this->independentSeries as $index => $series) {
            $actual = $this->dependentSeries[$index];
            $predicted = $this->predict($series);
            
            $sumSquaredError += pow($actual - $predicted, 2);
            $meanSquaredError += pow($actual - $mean, 2);
        }
        
        $this->r2 = 1 - $sumSquaredError / $meanSquaredError;
        $this->S = sqrt($sumSquaredError / $count);
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
     * regress
     * 
     * Performs the regression, setting the predictors array to the result of
     * the regression.
     */
    protected function regress()
    {
        $this->checkData();
        
        // Perform transformations
        
        $linearDependents = array_map([$this->dependentLinking, 'linearize'], $this->dependentSeries);
        
        $linearIndependents = [];
        
        foreach ($this->independentSeries as $series) {
            $transformed = [];
            
            foreach ($series as $index => $datum) {
                $transformed[] = isset($this->independentLinkings[$index]) ? $this->independentLinkings[$index]->linearize($datum) : $this->independentLinking->linearize($datum);
            }
            
            $linearIndependents[] = $transformed;
        }
        
        // Now that everything has been linearized, regress it.
        
        $this->predictors = $this->algorithm->regress($linearDependents, $linearIndependents);
    }
}