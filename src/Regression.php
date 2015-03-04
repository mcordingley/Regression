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
    // Great reference for most of these members:
    // http://facweb.cs.depaul.edu/sjost/csc423/documents/f-test-reg.htm
    
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
     * degreesFreedomError
     * 
     * The number of degrees of freedom of error for the model.
     * 
     * @var int
     */
    protected $degreesFreedomError;
    
    /**
     * degreesFreedomModel
     * 
     * The number of degrees of freedom of the model.
     * 
     * @var int
     */
    protected $degreesFreedomModel;
    
    /**
     * degreesFreedomTotal
     * 
     * The number of degrees of freedom.
     * 
     * @var int
     */
    protected $degreesFreedomTotal;
    
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
     * meanSquaredError
     * 
     * The mean-squared error, effectively the "average" of the corresponding sum of
     * squares.
     * 
     * @var float
     */
    protected $meanSquaredError;
    
    /**
     * meanSquaredModel
     * 
     * The mean-squared model, effectively the "average" of the corresponding sum of
     * squares.
     * 
     * @var float
     */
    protected $meanSquaredModel;
    
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
     * predicted values, a raw measure of the regression's overall error.
     * 
     * @var float
     */
    protected $sumSquaredError;
    
    /**
     * sumSquaredModel
     * 
     * The sum of the squared distances of the predicted observations from the
     * mean of the true observations, a raw measure of the regression's overall
     * explanatory power.
     * 
     * @var float
     */
    protected $sumSquaredModel;
    
    /**
     * sumSquaredTotal
     * 
     * The sum of the squared distances of the observations from their mean.
     * SST = SSE + SSM Useful measure to put the other two sum of squares measures
     * into context
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
            
            $k = sqrt($this->getSumSquaredError() / $this->getDegreesOfFreedomError());
            
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
        $this->sumSquaredModel = null;
        $this->sumSquaredTotal = null;
        
        $this->degreesFreedomError = null;
        $this->degreesFreedomModel = null;
        $this->degreesFreedomTotal = null;
        
        $this->meanSquaredError = null;
        $this->meanSquaredModel = null;
        
        $this->r2 = null;
        $this->S = null;
        $this->SCoefficients = null;
        $this->tCoefficients = null;
    }
    
    /**
     * getDegreesOfFreedomError
     * 
     * Returns the degrees of freedom of the error for this regression.
     * 
     * @return int
     */
    protected function getDegreesOfFreedomError()
    {
        if (is_null($this->degreesFreedomError)) {
            $observationCount = count($this->independentSeries);
            $explanatoryVariableCount = count($this->independentSeries[0]);
        
            $this->degreesFreedomError = $observationCount - $explanatoryVariableCount;
        }
        
        return $this->degreesFreedomError;
    }
    
    /**
     * getDegreesOfFreedomModel
     * 
     * Returns the degrees of freedom of the model for this regression.
     * 
     * @return int
     */
    protected function getDegreesOfFreedomModel()
    {
        if (is_null($this->degreesFreedomModel)) {
            $explanatoryVariableCount = count($this->independentSeries[0]);
        
            $this->degreesFreedomModel = $explanatoryVariableCount - 1;
        }
        
        return $this->degreesFreedomModel;
    }
    
    /**
     * getDegreesOfFreedomTotal
     * 
     * Returns the degrees of freedom for this regression.
     * 
     * @return int
     */
    protected function getDegreesOfFreedomTotal()
    {
        if (is_null($this->degreesFreedomTotal)) {
            $observationCount = count($this->independentSeries);
        
            $this->degreesFreedomTotal = $observationCount - 1;
        }
        
        return $this->degreesFreedomTotal;
    }
    
    /**
     * getMeanSquaredError
     * 
     * Returns the mean-squared error of the regression, which is effectively
     * the "average" of the corresponding sum of squares.
     * 
     * @return float
     */
    protected function getMeanSquaredError()
    {
        if (is_null($this->meanSquaredError)) {
            $this->meanSquaredError = $this->getSumSquaredError() / $this->getDegreesOfFreedomError();
        }
        
        return $this->meanSquaredError;
    }
    
    /**
     * getMeanSquaredModel
     * 
     * Returns the mean-squared model of the regression, which is effectively
     * the "average" of the corresponding sum of squares.
     * 
     * @return float
     */
    protected function getMeanSquaredModel()
    {
        if (is_null($this->meanSquaredModel)) {
            $this->meanSquaredModel = $this->getSumSquaredError() / $this->getDegreesOfFreedomError();
        }
        
        return $this->meanSquaredModel;
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
     * distances of the observations from their predicted values, a raw measure
     * of the overall error in the regression model.
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
     * getSumSquaredModel
     * 
     * Calculates the mean-squared error of the regression. This is the sum
     * of the squared distances of observations from their average.
     * 
     * @return float
     */
    protected function getSumSquaredModel()
    {
        if (is_null($this->sumSquaredModel)) {
            $mean = array_reduce($this->dependentSeries, function ($memo, $value) {
                return $memo + $value;
            }) / count($this->dependentSeries);
            
            $this->sumSquaredModel = array_reduce($this->getPredictedValues(), function ($memo, $value) use ($mean) {
                return $memo + pow($value - $mean, 2);
            }, 0);
        }
        
        return $this->sumSquaredModel;
    }
    
    /**
     * getSumSquaredTotal
     * 
     * Calculates the sum-squared total of the regression. This is the sum
     * of the squared distances of observations from their average, a useful
     * measure to put the sum-squared error (SSE) and sum-squared model (SSM)
     * into context.
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