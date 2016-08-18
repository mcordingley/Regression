<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;
use PHPUnit_Framework_TestCase;

class GradientNormTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $criteria = new GradientNorm(1.0);

        static::assertFalse($criteria->converged([2, 2], []));
        static::assertTrue($criteria->converged([0.5, 0.5], []));
    }
}