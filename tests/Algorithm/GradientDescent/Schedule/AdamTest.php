<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\Schedule;

use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adam;
use PHPUnit_Framework_TestCase;

class AdamTest extends PHPUnit_Framework_TestCase
{
    public function testStep()
    {
        $schedule = new Adam(0.01, 0.00000001, 0.9, 0.999);

        $schedule->update([5.0]);
        $schedule->update([5.0]);

        static::assertEquals(0.013438388840804524, $schedule->step(0));
    }
}