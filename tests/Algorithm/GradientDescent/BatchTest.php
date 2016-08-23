<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Batch;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;

class BatchTest extends GradientDescent
{
    /**
     * @return Algorithm
     */
    protected function makeRegression()
    {
        return new Batch(new LogisticGradient, new Fixed(0.125), new GradientNorm);
    }
}
