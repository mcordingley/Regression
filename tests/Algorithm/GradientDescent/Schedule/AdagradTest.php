<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\Schedule;

use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Adagrad;
use PHPUnit\Framework\TestCase;

class AdagradTest extends TestCase
{
    public function testStep()
    {
        $schedule = new Adagrad(0.01, 0.000001);
        $schedule->update([1.0]);
        static::assertEquals(0.0099999900000100009, $schedule->step(0));
    }
}
