<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\Gradient;

use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Gradient;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Regularized;
use PHPUnit_Framework_TestCase;

class RegularizedTest extends PHPUnit_Framework_TestCase
{
    public function testCost()
    {
        $gradient = $this->makeGradient();
        static::assertEquals(1.5, $gradient->cost([1.0, 1.0], [2.0, 2.0], 3.0));
    }

    /**
     * @return Regularized
     */
    private function makeGradient()
    {
        $mock = $this->getMockBuilder(Gradient::class)
            ->setMethods(['cost', 'gradient'])
            ->getMock();

        $mock->method('cost')->willReturn(1.0);
        $mock->method('gradient')->willReturn([16.0, 16.0]);

        $gradient = new Regularized($mock);

        return $gradient->ignoreFirstFeature(true)
                        ->setLambda(1.0)
                        ->setLevel(2);
    }

    public function testGradient()
    {
        $gradient = $this->makeGradient();
        static::assertEquals([16.0, 17.0], $gradient->gradient([1.0, 1.0], [2.0, 2.0], 3.0));
    }
}