<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\SteppedCriteria;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use PHPUnit_Framework_TestCase;

class SteppedCriteriaTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $mock = $this->getMockBuilder(StoppingCriteria::class)
            ->setMethods(['converged'])
            ->getMock();

        $mock->expects($this->once())
            ->method('converged')
            ->with([0.01, 0.02, 0.03])
            ->willReturn(true);

        $stepped = new SteppedCriteria($mock, new Fixed(0.01));
        $stepped->converged([1.0, 2.0, 3.0], []);
    }
}