<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linking;

/**
 * LinkingInterface
 * 
 * Defines a mapping of non-linear data into a linear space.
 */
interface LinkingInterface
{
    /**
     * delinearize
     * 
     * Transforms a linearized datum back into it's original non-linear form.
     * i.e. This function should be the inverse of `linearize`.
     * 
     * @param float $value
     * @return float
     */
    public function delinearize(float $value): float;
    
    /**
     * linearize
     * 
     * Transforms the datum to a linear form.
     * 
     * @param float $value
     * @return float
     */
    public function linearize(float $value): float;
}