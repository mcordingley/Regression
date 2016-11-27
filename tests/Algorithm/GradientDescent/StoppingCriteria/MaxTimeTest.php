<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\MaxTime;
use PHPUnit_Framework_TestCase;

class MaxTimeTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $criteria = new MaxTime(1);

        static::assertFalse($criteria->converged([], []));

        sleep(2);

        static::assertTrue($criteria->converged([], []));
    }
}
