<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Linkings\Identity;
use mcordingley\Regression\RegressionAlgorithms\GradientDescent;

class GradientDescentTest extends RegressionTest
{
    // Should be exactly the same as the regular Least Squares, but by a
    // different route.
    protected function makeRegression()
    {
        $this->regression = new GradientDescent(new Identity);
    }
}
