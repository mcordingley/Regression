<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\MaxIterations;
use PHPUnit_Framework_TestCase;

class MaxIterationsTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $criteria = new MaxIterations(5);

        static::assertFalse($criteria->converged([], []));
        static::assertFalse($criteria->converged([], []));
        static::assertFalse($criteria->converged([], []));
        static::assertFalse($criteria->converged([], []));
        static::assertTrue($criteria->converged([], []));
    }
}
