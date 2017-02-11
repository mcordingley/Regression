<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm;

use InvalidArgumentException;
use MCordingley\Regression\Data\Collection;

interface Algorithm
{
    /**
     * @param Collection $observations
     * @return array
     * @throws InvalidArgumentException
     */
    public function regress(Collection $observations): array;
}
