<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\StoppingCriteria;

use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\Any;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class AnyTest extends PHPUnit_Framework_TestCase
{
    public function testConverged()
    {
        $criteria = new Any([
            $this->makeMock(false),
            $this->makeMock(true),
        ]);

        static::assertTrue($criteria->converged([], []));
    }

    /**
     * @param $returnValue
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function makeMock($returnValue)
    {
        $mock = $this->getMockBuilder(StoppingCriteria::class)
            ->setMethods(['converged'])
            ->getMock();

        $mock->method('converged')->willReturn($returnValue);

        return $mock;
    }

    public function testNotConverged()
    {
        $criteria = new Any([
            $this->makeMock(false),
            $this->makeMock(false),
        ]);

        static::assertFalse($criteria->converged([], []));
    }
}