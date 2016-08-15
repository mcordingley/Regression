<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

interface DescentIterationListener
{
    /**
     * @param array $coefficients
     * @param float $averageCost
     * @return void
     */
    public function onGradientDescentIteration(array $coefficients, $averageCost);
}