<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent\Schedule;

use MCordingley\Regression\Algorithm\GradientDescent\Schedule\RmsProp;
use PHPUnit_Framework_TestCase;

class RmsPropTest extends PHPUnit_Framework_TestCase
{
    public function testStep()
    {
        $schedule = new RmsProp(0.9, 0.01, 0.000001);
        $schedule->update([1.0]);
        static::assertEquals(0.0099999950000037498, $schedule->step(0));
    }
}
