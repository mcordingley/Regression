<?php

declare(strict_types=1);

namespace mcordingley\Regression;

final class Predictor
{
    private $coefficients;
    private $outputTransformer;
    
    /**
     * 
     * @param CoefficientSet $coefficients The returned coefficients from a regression
     * @param OutputTransformer $outputTransformer
     */
    public function __construct(CoefficientSet $coefficients, OutputTransformer $outputTransformer = null)
    {
        $this->coefficients = $coefficients;
        $this->outputTransformer = $outputTransformer;
    }
    
    /**
     * predict
     * 
     * @param array $independents A set of observed independent variables
     * @return float
     */
    public function predict(array $independents): float
    {
        $transformedInputs = array_merge([1], $independents);
        $output = 0;
        
        foreach ($this->coefficients as $i => $coefficient) {
            $output += $transformedInputs[$i] * $coefficient;
        }
        
        if ($this->outputTransformer) {
            $output = $this->outputTransformer->delinearize($output);
        }
        
        return $output;
    }
}
