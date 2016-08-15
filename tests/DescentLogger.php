<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\DescentIterationListener;

class DescentLogger implements DescentIterationListener
{
    public function onGradientDescentIteration(array $coefficients, $averageCost)
    {
        var_dump([$coefficients, $averageCost]);
    }
}