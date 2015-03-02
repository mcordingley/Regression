<?php

namespace mcordingley\Regression\Linking;

use mcordingley\Regression\Linking;

/**
 * Exponential
 * 
 * Linking implementation that transforms data that follows an exponential curve
 * into and back out of linear space. No data points may have values that are
 * less than or equal to zero.
 */
class Exponential implements Linking
{
    protected $base;

    /**
     * __construct
     * 
     * @param float|null $base Base of the exponential function. Defaults to M_E.
     */
    public function __construct($base = M_E)
    {
        $this->base = $base;
    }
    
    public function delinearize($value)
    {
        return pow($this->base, $value);
    }
    
    public function linearize($value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Attempting to take the logarithm of a non-positive number. Double-check your regression model.');
        }
        
        return log($value, $this->base);
    }
}