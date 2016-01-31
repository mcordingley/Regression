<?php

declare(strict_types=1);

namespace mcordingley\Regression;

use mcordingley\Regression\RegressionAlgorithm\RegressionAlgorithmInterface;

/**
 * SimpleRegression
 * 
 * A facade (in the GoF sense) over the other regression classes, for when you
 * just want to run a linear regression and get data out with a minimum of
 * digging through the documentation. Includes special handling of the intercept
 * apart from the other predictors.
 */
final class SimpleRegression
{
    private $regression;

    /**
     * __construct
     */
    public function __construct(RegressionAlgorithmInterface $regressionStrategy = null)
    {
        $this->regression = new Regression($regressionStrategy);
    }
    
    // Proxy unmodified functions through to the wrapped `Regression` object.
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->regression, $name], $arguments);
    }
    
    /**
     * makeLogRegression
     * 
     * Factory function to return a regression object set up to perform
     * regressions against data fitted with the equation
     * 
     *     y = a + b1 * ln(x1) + b2 * ln(x2) + ... + bn * ln(xn)
     * 
     * @return static
     */
    public static function makeLogRegression(RegressionAlgorithmInterface $regressionStrategy = null): self
    {
        $regression = Regression::makeLogRegression($regressionStrategy);
        
        $simple = new static;
        $simple->regression = $regression;
        
        return $simple;
    }
    
    /**
     * makeExpRegression
     * 
     * Factory function to return a regression object set up to perform
     * regressions against data fitted with the equation
     * 
     *     y = a * b1^x1 * b2^x2 * ... * bn^xn
     * 
     * @return static
     */
    public static function makeExpRegression(RegressionAlgorithmInterface $regressionStrategy = null): self
    {
        $regression = Regression::makeExpRegression($regressionStrategy);
        
        $simple = new static;
        $simple->regression = $regression;
        
        return $simple;
    }
    
    /**
     * makePowerRegression
     * 
     * Factory function to return a regression object set up to perform
     * regressions against data fitted with the equation
     * 
     *     y = a * x1^b1 * x2^b2 * ... * xn^bn
     * 
     * @return static
     */
    public static function makePowerRegression(RegressionAlgorithmInterface $regressionStrategy = null): self
    {
        $regression = Regression::makePowerRegression($regressionStrategy);
        
        $simple = new static;
        $simple->regression = $regression;
        
        return $simple;
    }
    
    /**
     * addData
     * 
     * @param float $dependent The variable explained by $independentSeries.
     * @param array|float $independentSeries Array of explanatory variables or a single such variable.
     * @return self
     */
    public function addData(float $dependent, $independentSeries): self
    {
        if (!is_array($independentSeries)){
            $independentSeries = [$independentSeries];
        }
        
        $constantLinking = $this->regression->getIndependentLinking(0) ?: $this->regression->getIndependentLinking();
        $this->regression->addData($dependent, array_merge([$constantLinking->delinearize(1)], $independentSeries));
        
        return $this;
    }
    
    /**
     * getCoefficents
     * 
     * Returns the coefficients determined by the regression.
     * 
     * @return array
     */
    public function getCoefficients(): array
    {
        return array_slice($this->regression->getCoefficients(), 1);
    }
    
    /**
     * getIntercept
     * 
     * @return float
     */
    public function getIntercept(): float
    {
        $constantLinking = $this->regression->getIndependentLinking(0) ?: $this->regression->getIndependentLinking();
        return $constantLinking->delinearize($this->regression->getCoefficients()[0]);
    }
    
    /**
     * getStandardErrorCoefficients
     * 
     * Calculates the standard error of each of the regression coefficients.
     * 
     * @return array
     */
    public function getStandardErrorCoefficients(): array
    {
        return array_slice($this->regression->getStandardErrorCoefficients(), 1);
    }
    
    /**
     * getTStatistics
     * 
     * Calculates the t test values of each of the regression coefficients.
     * 
     * @return array
     */
    public function getTStatistics(): array
    {
        return array_slice($this->regression->getTStatistics(), 1);
    }
    
    /**
     * predict
     * 
     * @param array|float $series Data with which to make a prediction.
     * @param array|null $coefficients Alternate set of coefficients to use.
     * @return float The predicted value.
     */
    public function predict($series, array $coefficients = null): float
    {
        if (!is_array($series)) {
            $series = [$series];
        }
        
        $constantLinking = $this->regression->getIndependentLinking(0) ?: $this->regression->getIndependentLinking();
        return $this->regression->predict(array_merge([$constantLinking->delinearize(1)], $series), $coefficients);
    }
}
