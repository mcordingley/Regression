<?php

namespace MCordingley\Regression\Tests;

use MCordingley\Regression\Observation;
use PHPUnit\Framework\TestCase;

class ObservationTest extends TestCase
{
    public function testGetters()
    {
        $observation = new Observation([1, 2], 3);

        static::assertEquals([1, 2], $observation->getFeatures());
        static::assertEquals(3, $observation->getOutcome());
    }
}
