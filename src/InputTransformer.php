<?php

declare(strict_types=1);

namespace mcordingley\Regression;

interface InputTransformer
{
    /**
     * linearize
     *
     * Converts the incoming value into a linear space suitable for regression.
     *
     * @param float $data
     * @return float
     */
    public function linearize(float $data): float;
}
