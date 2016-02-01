<?php

declare(strict_types=1);

namespace mcordingley\Regression;

final class Predictor
{
    private $inputTransformer;
    private $outputTransformer;
    
    public function __construct(InputTransformer $inputTransformer, OutputTransformer $outputTransformer)
    {
        $this->outputTransformer = $outputTransformer;
        $this->inputTransformer = $inputTransformer;
    }
    
    /**
     * predict
     * 
     * @param array $independents A set of observed independent variables
     * @param CoefficientSet $coefficients The returned coefficients from a regression
     * @return float
     */
    public function predict(array $independents, CoefficientSet $coefficients): float
    {
        $transformedInputs = array_merge([1], array_map([$this->inputTransformer, 'linearize'], $independents);
        
        $sumProduct = 0;
        
        foreach ($coefficients as $i => $coefficient) {
            $sumProduct += $transformedInputs[$i] * $coefficient;
        }
        
        return $this->outputTransformer->delinearize($sumProduct);
    }
}
