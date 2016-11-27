<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent;

use MCordingley\Regression\Algorithm\Algorithm;
use MCordingley\Regression\Algorithm\GradientDescent\Batch;
use MCordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Adam;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;

class BatchTest extends GradientDescent
{
    /**
     * @return Algorithm
     */
    protected function makeRegression()
    {
        return new Batch(new LogisticGradient, new Adam, new GradientNorm);
    }

    /**
     * @return array
     */
    protected function getExpectedCoefficients()
    {
        return [
            -3.9574854804237094,
            0.22580894980156149,
            0.79622399659931598,
            -0.67753634191079093,
            -1.3413824186724197,
            -1.5537312883686745,
        ];
    }
}
