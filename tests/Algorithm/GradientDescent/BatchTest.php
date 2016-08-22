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

    /**
     * @return array
     */
    protected function getExpectedCoefficients()
    {
        return [
            -3.9572690927850793,
            0.22579298444865589,
            0.79626535291848777,
            -0.67784339995776333,
            -1.3416834110939926,
            -1.55412650298527
        ];
    }
}