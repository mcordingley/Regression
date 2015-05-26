<?php

namespace mcordingley\Regression;

use InvalidArgumentException;
use mcordingley\Regression\Linking\Exponential;
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
     * tStatistics
     * 
     * This is an array of the t statistics for each calculated coefficient.
     * 
     * @var array
     */
    protected $tStatistics;
    
    /**
     * __construct
     * 
     * @param RegressionAlgorithmInterface|null A regression strategy to perform the calculations
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
     * makeLogRegression
     * 
     * Factory function to return a regression object set up to perform
     * regressions against data fitted with the equation
     * 
     *     y = b1 * ln(x1) + b2 * ln(x2) + ... + bn * ln(xn)
     * 
     * Note that when using this, the identity value for any constant data points
     * is M_E, not 1 as is usually the case.
     * 
     * @param RegressionAlgorithmInterface|null $regressionStrategy
     * @return static
     */
    public static function makeLogRegression(RegressionAlgorithmInterface $regressionStrategy = null)
    {
        $regression = new static($regressionStrategy);
        
        $regression->setIndependentLinking(new Exponential);
        
        return $regression;
    }
    
    /**
     * makeExpRegression
     * 
     * Factory function to return a regression object set up to perform
     * regressions against data fitted with the equation
     * 
     *     y = b1^x1 * b2^x2 * ... * bn^xn
     * 
     * @param RegressionAlgorithmInterface|null $regressionStrategy
     * @return static
     */
    public static function makeExpRegression(RegressionAlgorithmInterface $regressionStrategy = null)
    {
        $regression = new static($regressionStrategy);
        
        $regression->setDependentLinking(new Exponential);
        
        return $regression;
    }
    
    /**
     * makePowerRegression
     * 
     * Factory function to return a regression object set up to perform
     * regressions against data fitted with the equation
     * 
     *     y = x1^b1 * x2^b2 * ... * xn^bn
     * 
     * Note that when using this, the identity value for any constant data points
     * is M_E, not 1 as is usually the case.
     * 
     * @param RegressionAlgorithmInterface|null $regressionStrategy
     * @return static
     */
    public static function makePowerRegression(RegressionAlgorithmInterface $regressionStrategy = null)
    {
        $linking = new Exponential;
        $regression = new static($regressionStrategy);
        
        $regression->setIndependentLinking($linking);
        $regression->setDependentLinking($linking);
        
        return $regression;
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
            $linearDependents = array_map([$this->dependentLinking, 'linearize'], $this->dependentSeries);
            
            $linearIndependents = array_map(function ($series) {
                $transformed = [];

                foreach ($series as $index => $datum) {
                    $transformed[] = isset($this->independentLinkings[$index]) ? $this->independentLinkings[$index]->linearize($datum) : $this->independentLinking->linearize($datum);
                }

                return $transformed;
            }, $this->independentSeries);

            $this->coefficients = $this->algorithm->regress($linearDependents, $linearIndependents);
        }
        
        return $this->coefficients;
    }
    
    /**
     * getDegreesOfFreedomError
     * 
     * Returns the degrees of freedom of the error for this regression.
     * 
     * @return int
     */
    public function getDegreesOfFreedomError()
    {
        // Obervations minus explanatory variables
        return count($this->independentSeries) - count($this->independentSeries[0]);
    }
    
    /**
     * getDegreesOfFreedomModel
     * 
     * Returns the degrees of freedom of the model for this regression.
     * 
     * @return int
     */
    public function getDegreesOfFreedomModel()
    {
        // One less than the number of explanatory variables
        return count($this->independentSeries[0]) - 1;
    }
    
    /**
     * getDependentLinking
     * 
     * @return LinkingInterface
     */
    public function getDependentLinking()
    {
        return $this->dependentLinking;
    }
    
    /**
     * getFStatistic
     * 
     * Returns the F statistic, which is compared against the F distribution CDF
     * to determine if the regression is "significant" or not.
     * 
     * @return float
     */
    public function getFStatistic()
    {
        return $this->getMeanSquaredModel() / $this->getMeanSquaredError();
    }
    
    /**
     * getIndependentLinking
     * 
     * Returns the linking used at the specified index if different than the
     * default linking used for independent variables. If a linking isn't set
     * specifically for the requested index, returns `null`. If $index is not
     * specified, returns the linking used by default for all independent
     * variables.
     * 
     * @param int|null $index If specified, returns the linking for this specific index.
     * @return LinkingInterface|null
     */
    public function getIndependentLinking($index = null)
    {
        if (is_null($index)) {
            return $this->independentLinking;
        } else {
            return isset($this->independentLinkings[$index]) ? $this->independentLinkings[$index] : null;
        }
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
        $sumSquaredTotal = $this->getSumSquaredTotal();
        
        if ($sumSquaredTotal === 0) {
            return 0;
        }
        
        return 1 - $this->getSumSquaredError() / $sumSquaredTotal;
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
        return sqrt($this->getSumSquaredError() / count($this->dependentSeries));
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
            $meanError = sqrt($this->getMeanSquaredError());
            
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
                
                $this->SCoefficients[] = $meanError / sqrt($sseX);
            }
        }
        
        return $this->SCoefficients;
    }
    
    /**
     * getTStatistics
     * 
     * Calculates the t test values of each of the regression coefficients.
     * 
     * @return array
     */
    public function getTStatistics()
    {
        if (is_null($this->tStatistics)) {
            $predictors = $this->getCoefficients();
            $SCoefficients = $this->getStandardErrorCoefficients();
            
            $this->tStatistics = [];

            for ($i = 0, $len = count($predictors); $i < $len; $i++) {
                $this->tStatistics[] = $predictors[$i] / $SCoefficients[$i];
            }
        }
        
        return $this->tStatistics;
    }

    /**
     * predict
     * 
     * Uses the calculated coefficients from this regression to make a
     * prediction. If the optional `$coefficients` argument is supplied, uses
     * that instead of the calculated values. This would be useful for reusing
     * stored coefficients from a previous regression.
     * 
     * @param array $series Data with which to make a prediction.
     * @param array|null $coefficients Alternate set of coefficients to use.
     * @return float The predicted value.
     */
    public function predict(array $series, array $coefficients = null)
    {
        $transformed = [];
        $coefficients = $coefficients ?: $this->getCoefficients();
        
        // Convert `$series` into linear space for computation.
        foreach ($series as $index => $datum) {
            $transformed[] = isset($this->independentLinkings[$index]) ? $this->independentLinkings[$index]->linearize($datum) : $this->independentLinking->linearize($datum);
        }
        
        // Multiply the datums from the series with the predictors and then sum
        // up to get the predicted value.
        
        $products = array_map(function ($predictor, $datum) {
            return $predictor * $datum;
        }, $coefficients, $transformed);
        
        $sumProduct = array_reduce($products, function($memo, $product) {
            return $memo + $product;
        }, 0);
        
        // Transform back out of linear space for the answer to be meaningful.
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
    public function setDependentLinking(LinkingInterface $linking)
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
    public function setIndependentLinking(LinkingInterface $linking, $index = null)
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
        $this->SCoefficients = null;
        $this->tStatistics = null;
        
        $this->sumSquaredError = null;
        $this->sumSquaredModel = null;
        $this->sumSquaredTotal = null;
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
        // One less than observations
        return count($this->independentSeries) - 1;
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
        return $this->getSumSquaredError() / $this->getDegreesOfFreedomError();
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
        return $this->getSumSquaredError() / $this->getDegreesOfFreedomError();
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