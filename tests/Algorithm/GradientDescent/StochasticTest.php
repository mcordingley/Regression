<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\Algorithm;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adam;
use mcordingley\Regression\Algorithm\GradientDescent\Stochastic;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;

class StochasticTest extends GradientDescent
{
    /**
     * @return Algorithm
     */
    protected function makeRegression()
    {
        static::markTestSkipped('Yet to find a good convergence criteria for this type of regression.');

        return new Stochastic(new LogisticGradient, new Adam, new GradientNorm);
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