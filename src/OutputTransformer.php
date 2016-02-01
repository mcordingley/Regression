<?php

declare(strict_types=1);

namespace mcordingley\Regression;

interface OutputTransformer
{
    /**
     * delinearize
     *
     * Converts the incoming value out of linear space suitable for interpretation.
     *
     * @param float $data
     * @return float
     */
    public function delinearize(float $data): float;
}
