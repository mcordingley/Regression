<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\CoefficientEquality;
use PHPUnit_Framework_TestCase;

class CoefficientEqualityTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $criteria = new CoefficientEquality;

        static::assertFalse($criteria->converged([], [1, 2]));
        static::assertFalse($criteria->converged([], [1, 2, 3]));
        static::assertTrue($criteria->converged([], [1, 2, 3]));
    }
}