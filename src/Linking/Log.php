<?php

namespace mcordingley\Regression\Linking;

use InvalidArgumentException;

/**
 * Log
 * 
 * Linking implementation that transforms data that follows a logarithmic curve
 * into and out of linear space.
 */
class Log implements LinkingInterface
{
    protected $base;

    /**
     * __construct
     * 
     * @param float|null $base Base of the logarithmic function. Defaults to M_E.
     */
    public function __construct($base = M_E)
    {
        $this->base = $base;
    }
    
    public function delinearize($value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Attempting to take the logarithm of a non-positive number. Double-check your regression model.');
        }
        
        return log($value, $this->base);
    }
    
    public function linearize($value)
    {
        return pow($this->base, $value);
    }
}