<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\MaxTime;
use PHPUnit\Framework\TestCase;

class MaxTimeTest extends TestCase
{
    public function testConverged()
    {
        $criteria = new MaxTime(1);

        static::assertFalse($criteria->converged([], []));

        sleep(2);

        static::assertTrue($criteria->converged([], []));
    }
}
