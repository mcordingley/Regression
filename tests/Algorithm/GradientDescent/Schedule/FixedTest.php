<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent\Schedule;

use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use PHPUnit_Framework_TestCase;

class FixedTest extends PHPUnit_Framework_TestCase
{
    public function testStep()
    {
        $schedule = new Fixed(1.0);
        $schedule->update([1.0]);
        static::assertEquals(1.0, $schedule->step(0));
    }
}
