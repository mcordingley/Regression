<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\Gradient;

use MCordingley\Regression\Algorithm\GradientDescent\Gradient\Linear;
use PHPUnit_Framework_TestCase;

class LinearTest extends PHPUnit_Framework_TestCase
{
    public function testCost()
    {
        $gradient = new Linear(2);
        static::assertEquals(1.0, $gradient->cost([1.0], [2.0], 3.0));
    }

    public function testGradient()
    {
        $gradient = new Linear(2);
        static::assertEquals([-4.0], $gradient->gradient([1.0], [2.0], 3.0));
    }
}