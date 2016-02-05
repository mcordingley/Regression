<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linkings;

use InvalidArgumentException;
use mcordingley\Regression\OutputTransformer;

/**
 * Log
 *
 * Linking implementation that transforms data that follows a logarithmic curve
 * into and out of linear space.
 */
final class Log implements OutputTransformer
{
    private $base;

    /**
     * __construct
     *
     * @param float|null $base Base of the logarithmic function. Defaults to M_E.
     */
    public function __construct(float $base = M_E)
    {
        $this->base = $base;
    }

    public function delinearize(float $value): float
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Attempting to take the logarithm of a non-positive number. Double-check your regression model.');
        }

        return log($value, $this->base);
    }

    public function linearize(float $value): float
    {
        return pow($this->base, $value);
    }
}
