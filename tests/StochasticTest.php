<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\Stochastic;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adagrad;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Linear;
use mcordingley\Regression\Observations;
use PHPUnit_Framework_TestCase;

/**
 * Integration test to demonstrate the equivalence of this method of regression to the analytic LinearLeastSquares.
 * This method is favored for extremely large data sets.
 *
 * @package mcordingley\Regression\Tests
 * @see mcordingley\Regression\Tests\Algorithm\LeastSquaresTest
 */
class StochasticTest extends PHPUnit_Framework_TestCase
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
        $regression = new Stochastic(new Linear, new Adagrad);
        $coefficients = $regression->regress(Observations::fromArray(static::$features, static::$outcomes));

        $this->assertEquals(1.095, round($coefficients[0], 3));
        $this->assertEquals(0.925, round($coefficients[1], 3));
    }
}