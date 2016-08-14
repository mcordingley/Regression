<?php

namespace mcordingley\Regression\Algorithm;

use mcordingley\Regression\Observations;

interface Algorithm
{
    /**
     * @param Observations $observations
     * @return array
     */
    public function regress(Observations $observations);
}
