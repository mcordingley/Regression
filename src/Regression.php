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
     * coefficients
     * 
     * The calculated beta values that show what the contribution of each
     * explanatory variable is to the overall fitted curve.
     * 
     * @var array
     */
    protected $coefficients;
    
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
     * predictedValues
     * 
     * What the observed values would be if predicted by the model.
     * 
     * @var array
     */
    protected $predictedValues;
    
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
     * SCoefficients
     * 
     * This is an array of the standard errors for each calculated coefficient.
     * 
     * @var array
     */
    protected $SCoefficients;
    
    /**
     * sumSquaredError
     * 
     * This is the sum of squared distances of observations from their
     * predicted values.
     * 
     * @var float
     */
    protected $sumSquaredError;
    
    /**
     * sumSquaredTotal
     * 
     * The sum of the squared distances of the observations from their mean.
     * 
     * @var float
     */
    protected $sumSquaredTotal;
    
    /**
     * tValues
     * 
     * This is an array of the t statistics for each calculated coefficient.
     * 
     * @var array
     */
    protected $tValues;
    
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
        
        $this->clearCalculations();
        
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
        if (is_null($this->coefficients)) {
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

            $this->coefficients = $this->algorithm->regress($linearDependents, $linearIndependents);
        }
        
        return $this->coefficients;
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
            $sumSquaredTotal = $this->getSumSquaredTotal();
            if ($sumSquaredTotal === 0) {
                $this->r2 = 0;
            } else {
                $this->r2 = 1 - $this->getSumSquaredError() / $sumSquaredTotal;
            }
        }
        
        return $this->r2;
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
        if (is_null($this->S)) {
            $this->S = sqrt($this->getSumSquaredError() / count($this->dependentSeries));
        }
        
        return $this->S;
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
        if (is_null($this->SCoefficients)) {
            $this->SCoefficients = [];
            
            $observationCount = count($this->dependentSeries);
            $variableCount = count($this->independentSeries[0]);
            
            $k = sqrt($this->getSumSquaredError() / $this->getDegreesOfFreedom());
            
            for ($variableIndex = 0; $variableIndex < $variableCount; $variableIndex++) {
                $sumX = 0;
                
                for ($observationIndex = 0; $observationIndex < $observationCount; $observationIndex++) {
                    $sumX += $this->independentSeries[$observationIndex][$variableIndex];
                }
                
                $averageX = $sumX / $observationCount;
                
                $sseX = 0;
                
                for ($observationIndex = 0; $observationIndex < $observationCount; $observationIndex++) {
                    $sseX += pow($this->independentSeries[$observationIndex][$variableIndex] - $averageX, 2);
                }
                
                $this->SCoefficients[] = $k / sqrt($sseX);
            }
        }
        
        return $this->SCoefficients;
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
        if (is_null($this->tValues)) {
            $predictors = $this->getCoefficients();
            $SCoefficients = $this->getStandardErrorCoefficients();
            
            $this->tValues = [];

            for ($i = 0, $len = count($predictors); $i < $len; $i++) {
                $this->tValues[$i] = $predictors[$i] / $SCoefficients[$i];
            }
        }
        
        return $this->tValues;
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
     * clearCalculations
     * 
     * Clears out all of the derived data about this regression, as it has
     * been rendered no longer accurate.
     */
    protected function clearCalculations()
    {
        $this->coefficients = null;
        $this->predictedValues = null;
        $this->sumSquaredError = null;
        $this->sumSquaredTotal = null;
        $this->r2 = null;
        $this->S = null;
        $this->SCoefficients = null;
        $this->tCoefficients = null;
    }
    
    /**
     * getDegreesOfFreedom
     * 
     * Returns the degrees of freedom for this regression.
     * 
     * @return int
     */
    protected function getDegreesOfFreedom()
    {
        $observationCount = count($this->independentSeries);
        
        if (!$observationCount) {
            return 0;
        }
        
        $explanatoryVariableCount = count($this->independentSeries[0]);
        
        return $observationCount - $explanatoryVariableCount;
    }
    
    /**
     * getPredictedValues
     * 
     * Calculates what the model would predict for each of the observed values.
     * 
     * @return array
     */
    protected function getPredictedValues()
    {
        if (is_null($this->predictedValues)) {
            $this->predictedValues = [];
            
            foreach ($this->independentSeries as $series) {
                $this->predictedValues[] = $this->predict($series);
            }
        }
        
        return $this->predictedValues;
    }
    
    /**
     * getSumSquaredError
     * 
     * Calculates the sum of the squares of the residuals, which are the
     * distances of the observations from their predicted values.
     * 
     * @return float
     */
    protected function getSumSquaredError()
    {
        if (is_null($this->sumSquaredError)) {
            $predictedValues = $this->getPredictedValues();
        
            $this->sumSquaredError = 0;
            
            foreach ($this->dependentSeries as $index => $observation) {
                $this->sumSquaredError += pow($observation - $predictedValues[$index], 2);
            }
        }
        
        return $this->sumSquaredError;
    }
    
    /**
     * getSumSquaredTotal
     * 
     * Calculates the mean-squared error of the regression. This is the sum
     * of the squared distances of observations from their average.
     * 
     * @return float
     */
    protected function getSumSquaredTotal()
    {
        if (is_null($this->sumSquaredTotal)) {
            $mean = array_reduce($this->dependentSeries, function ($memo, $value) {
                return $memo + $value;
            }) / count($this->dependentSeries);
            
            $this->sumSquaredTotal = array_reduce($this->dependentSeries, function ($memo, $value) use ($mean) {
                return $memo + pow($value - $mean, 2);
            }, 0);
        }
        
        return $this->sumSquaredTotal;
    }
}