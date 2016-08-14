<?php

namespace mcordingley\Regression\Algorithm;

use InvalidArgumentException;
use mcordingley\Regression\Observations;

interface Algorithm
{
    /**
     * @param Observations $observations
     * @return array
     * @throws InvalidArgumentException
     */
    public function regress(Observations $observations);
}
