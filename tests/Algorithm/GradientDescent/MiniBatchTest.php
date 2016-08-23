<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Algorithm\GradientDescent\MiniBatch;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adam;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;

class MiniBatchTest extends GradientDescent
{
    /**
     * @return Algorithm
     */
    protected function makeRegression()
    {
        static::markTestSkipped('Yet to find a good convergence criteria for this type of regression.');

        return new MiniBatch(new LogisticGradient, new Adam, new GradientNorm, 3);
    }
}
