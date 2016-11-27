<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\NthIteration;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use PHPUnit_Framework_TestCase;

class NthIterationTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $mock = $this->getMockBuilder(StoppingCriteria::class)
            ->setMethods(['converged'])
            ->getMock();

        $mock->method('converged')
            ->willReturn(true);

        $criteria = new NthIteration($mock, 3);

        static::assertFalse($criteria->converged([], []));
        static::assertFalse($criteria->converged([], []));
        static::assertTrue($criteria->converged([], []));
        static::assertFalse($criteria->converged([], []));
        static::assertFalse($criteria->converged([], []));
        static::assertTrue($criteria->converged([], []));
    }
}