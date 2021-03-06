<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\CoefficientEquality;
use PHPUnit\Framework\TestCase;

class CoefficientEqualityTest extends TestCase
{
    public function testConverged()
    {
        $criteria = new CoefficientEquality;

        static::assertFalse($criteria->converged([], [1, 2]));
        static::assertFalse($criteria->converged([], [1, 2, 3]));
        static::assertTrue($criteria->converged([], [1, 2, 3]));
    }
}