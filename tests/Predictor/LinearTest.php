<?php

namespace MCordingley\Regression\Tests\Predictor;

use MCordingley\Regression\Predictor\Linear;
use PHPUnit\Framework\TestCase;

class LinearTest extends TestCase
{
    public function testPredict()
    {
        $predictor = new Linear([1.0954970633022, 0.92451598868827]);
        static::assertEquals(5.72, round($predictor->predict([1, 5]), 2));
    }
}