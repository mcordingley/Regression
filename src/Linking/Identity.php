<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linking;

/**
 * Identity
 * 
 * Default implementation of Linking that returns data untransformed. Used for
 * data that is already linear.
 */
final class Identity implements LinkingInterface
{
    public function delinearize(float $value): float
    {
        return $value;
    }
    
    public function linearize(float $value): float
    {
        return $value;
    }
}