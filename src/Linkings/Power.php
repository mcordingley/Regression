<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linkings;

use mcordingley\Regression\OutputTransformer;

/**
 * Power
 *
 * Linking implementation that transforms data that follows a geometric curve.
 */
final class Power implements OutputTransformer
{
    private $exponent;

    /**
     * __construct
     *
     * @param float|null $exponent The exponent that best describes the progression that the data follows. Defaults to 2.
     */
    public function __construct(float $exponent = 2.0)
    {
        $this->exponent = $exponent;
    }

    public function delinearize(float $value): float
    {
        return pow($value, $this->exponent);
    }

    public function linearize(float $value): float
    {
        return pow($value, 1 / $this->exponent);
    }
}
