<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;
use PHPUnit\Framework\TestCase;

class GradientNormTest extends TestCase
{
    public function testConverged()
    {
        $criteria = new GradientNorm(1.0);

        static::assertFalse($criteria->converged([2, 2], []));
        static::assertTrue($criteria->converged([0.5, 0.5], []));
    }
}