<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Linear;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adagrad;
use mcordingley\Regression\Algorithm\GradientDescent\Stochastic;

class GradientDescentTest extends RegressionTest
{
    // Should be exactly the same as the regular Least Squares, but by a
    // different route.
    protected function makeRegression()
    {
        $this->regression = new Stochastic(new Linear, new Adagrad);
    }
}
