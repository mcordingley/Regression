<?php

namespace mcordingley\Regression\Linking;

use InvalidArgumentException;

/**
 * Exponential
 * 
 * Linking implementation that transforms data that follows an exponential curve
 * into and back out of linear space. No data points may have values that are
 * less than or equal to zero.
 *
 * Note that the identity value to use for constant independent data series with
 * this linking is the base of the exponent instead of `1`.
 */
class Exponential implements LinkingInterface
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