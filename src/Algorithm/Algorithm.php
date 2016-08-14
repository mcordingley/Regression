<?php

namespace mcordingley\Regression\Algorithm;

use mcordingley\Regression\Observations;

interface Algorithm
{
    /**
     * @param Observations $data
     * @return array
     */
    public function regress(Observations $data);
}
