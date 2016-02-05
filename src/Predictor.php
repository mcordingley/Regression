<?php

declare(strict_types=1);

namespace mcordingley\Regression;

final class Predictor
{
    private $coefficients;
    private $outputTransformer;
    
    /**
     * 
     * @param array $coefficients The returned coefficients from a regression
     * @param OutputTransformer $outputTransformer
     */
    public function __construct(array $coefficients, OutputTransformer $outputTransformer = null)
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
        $output = Helpers::sumProduct($coefficients->toArray(), array_merge([1], $independents));
        
        if ($this->outputTransformer) {
            $output = $this->outputTransformer->delinearize($output);
        }
        
        return $output;
    }
}
