<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\Schedule;

use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use MCordingley\Regression\Algorithm\GradientDescent\Schedule\ExponentialDecay;
use PHPUnit\Framework\TestCase;

class ExponentialDecayTest extends TestCase
{
    public function testStep()
    {
        $schedule = new ExponentialDecay(new Fixed(1.0), 2, 2);

        $schedule->update([1.0]);
        $schedule->update([1.0]);

        static::assertEquals(0.5, $schedule->step(0));
    }
}