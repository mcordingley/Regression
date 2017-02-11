<?php

namespace MCordingley\Regression\Tests\Algorithm;

use MCordingley\Regression\Algorithm\LeastSquares;
use MCordingley\Regression\Observations;
use MCordingley\Regression\Tests\LeastSquaresFeatures;
use PHPUnit_Framework_TestCase;

class LeastSquaresTest extends PHPUnit_Framework_TestCase
{
    use LeastSquaresFeatures;

    public function testRegression()
    {
        $regression = new LeastSquares;
        $coefficients = $regression->regress(Observations::fromArray($this->getFeatures(), $this->getOutcomes()));

        $this->assertEquals(1.095, round($coefficients[0], 3));
        $this->assertEquals(0.925, round($coefficients[1], 3));
    }

    public function testTooFewObservations()
    {
        static::expectException('InvalidArgumentException');

        $regression = new LeastSquares;
        $regression->regress(Observations::fromArray([[1, 1]], [1]));
    }
}