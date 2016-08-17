<?php

namespace mcordingley\Regression\Tests\Algorithm;

use mcordingley\Regression\Observations;
use mcordingley\Regression\Algorithm\LeastSquares;
use PHPUnit_Framework_TestCase;

class LeastSquaresTest extends PHPUnit_Framework_TestCase
{
    private static $features = [
        [1, 1],
        [1, 2],
        [1, 1.3],
        [1, 3.75],
        [1, 2.25],
    ];

    private static $outcomes = [
        1,
        2,
        3,
        4,
        5,
    ];

    public function testRegression()
    {
        $regression = new LeastSquares;
        $coefficients = $regression->regress(Observations::fromArray(static::$features, static::$outcomes));

        $this->assertEquals(1.095, round($coefficients[0], 3));
        $this->assertEquals(0.925, round($coefficients[1], 3));
    }

    public function testTooFewObservations()
    {
        static::setExpectedException('InvalidArgumentException');

        $regression = new LeastSquares;
        $regression->regress(Observations::fromArray([[1, 1]], [1]));
    }
}