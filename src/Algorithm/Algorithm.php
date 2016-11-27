<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm;

use InvalidArgumentException;
use MCordingley\Regression\Observations;

interface Algorithm
{
    /**
     * @param Observations $observations
     * @return array
     * @throws InvalidArgumentException
     */
    public function regress(Observations $observations): array;
}
