<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\GradientDescent\Batch;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Linear;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adagrad;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;
use mcordingley\Regression\Observations;
use PHPUnit_Framework_TestCase;

class BatchTest extends PHPUnit_Framework_TestCase
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

    /**
     * @large
     */
    public function testRegression()
    {
        $regression = new Batch(new Linear, new Adagrad, new GradientNorm);
        $coefficients = $regression->regress(Observations::fromArray(static::$features, static::$outcomes));

        $this->assertEquals(1.095, round($coefficients[0], 3));
        $this->assertEquals(0.925, round($coefficients[1], 3));
    }
}