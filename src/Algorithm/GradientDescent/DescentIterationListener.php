<?php

namespace mcordingley\Regression\Algorithm\GradientDescent;

interface DescentIterationListener
{
    /**
     * @param array $coefficients
     * @return void
     */
    public function onGradientDescentIteration(array $coefficients);
}