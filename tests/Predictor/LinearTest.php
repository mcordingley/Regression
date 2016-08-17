<?php

namespace mcordingley\Regression\Tests\Predictor;

use mcordingley\Regression\Predictor\Linear;
use PHPUnit_Framework_TestCase;

class LinearTest extends PHPUnit_Framework_TestCase
{
    public function testPredict()
    {
        $predictor = new Linear([1.0954970633022, 0.92451598868827]);
        static::assertEquals(5.72, round($predictor->predict([1, 5]), 2));
    }
}