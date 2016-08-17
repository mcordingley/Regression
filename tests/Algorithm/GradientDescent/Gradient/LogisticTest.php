<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\Gradient;

use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic;
use PHPUnit_Framework_TestCase;

class LogisticTest extends PHPUnit_Framework_TestCase
{
    public function testCost()
    {
        $gradient = new Logistic;
        static::assertEquals(-3.8730719889570246, $gradient->cost([1.0], [2.0], 3.0));
    }

    public function testGradient()
    {
        $gradient = new Logistic;
        static::assertEquals([-4.2384058440442356], $gradient->gradient([1.0], [2.0], 3.0));
    }
}