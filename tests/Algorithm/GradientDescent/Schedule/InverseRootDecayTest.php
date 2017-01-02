<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\Schedule;

use MCordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use MCordingley\Regression\Algorithm\GradientDescent\Schedule\InverseRootDecay;
use PHPUnit_Framework_TestCase;

class InverseRootDecayTest extends PHPUnit_Framework_TestCase
{
    public function testStep()
    {
        $schedule = new InverseRootDecay(new Fixed(1.0));

        $schedule->update([1.0]);
        static::assertEquals(1.0, $schedule->step(0));

        $schedule->update([1.0]);
        static::assertEquals(1 / sqrt(2), $schedule->step(0));

        $schedule->update([1.0]);
        static::assertEquals(1 / sqrt(3), $schedule->step(0));
    }
}
