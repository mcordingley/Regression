<?php

declare(strict_types=1);

namespace mcordingley\Regression;

use mcordingley\Regression\Linking\Identity;

final class Predictor
{
    private $coefficients;
    private $inputTransformer;
    private $outputTransformer;
    
    /**
     * __construct
     * 
     * @param CoefficientSet $coefficients
     * @param OutputTransformer $outputTransformer
     * @param InputTransformer $inputTransformer
     */
    public function __construct(CoefficientSet $coefficients, OutputTransformer $outputTransformer = null, InputTransformer $inputTransformer = null)
    {
        $identity = new Identity;
        
        $this->coefficients = $coefficients;
        $this->outputTransformer = $outputTransformer ?: $identity;
        $this->inputTransformer = $inputTransformer ?: $identity;
    }
    
    /**
     * predict
     * 
     * @param array $independent
     * @return float
     */
    public function predict(array $independent): float
    {
        $transformed = [1];
        
        foreach ($independent as $datum) {
            $transformed[] = $this->inputTransformer->linearize($datum);
        }
        
        $products = array_map(function ($predictor, $datum) {
            return $predictor * $datum;
        }, $this->coefficients, $transformed);
        
        return $this->outputTransformer->delinearize(array_sum($products));
    }
}