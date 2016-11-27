<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\DescentSpy;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use PHPUnit_Framework_TestCase;
use stdClass;

class DescentSpyTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $mock = $this->getMockBuilder(StoppingCriteria::class)
            ->setMethods(['converged'])
            ->getMock();

        $mock->expects($this->once())
            ->method('converged')
            ->with([1], [2]);

        $calledObject = $this->getMockBuilder(stdClass::class)
            ->setMethods(['called'])
            ->getMock();

        $calledObject->expects($this->once())
            ->method('called')
            ->with([[1], [2]]);

        $criteria = new DescentSpy($mock, function (array $gradient, array $coefficients) use ($calledObject) {
            $calledObject->called(func_get_args());
        });

        $criteria->converged([1], [2]);
    }
}