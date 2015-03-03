<?php

namespace mcordingley\Regression\Linking;

/**
 * Power
 * 
 * Linking implementation that transforms data that follows a geometric curve.
 */
class Power implements LinkingInterface
{
    protected $exponent;
    
    /**
     * __construct
     * 
     * @param float|null $exponent The exponent that best describes the progression that the data follows. Defaults to 2.
     */
    public function __construct($exponent = 2)
    {
        $this->exponent = $exponent;
    }
    
    public function delinearize($value)
    {
        return pow($value, 1 / $this->exponent);
    }
    
    public function linearize($value)
    {
        return pow($value, $this->exponent);
    }
}