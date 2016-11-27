<?php

namespace MCordingley\Regression\Tests;

use MCordingley\Regression\Observation;
use PHPUnit_Framework_TestCase;

class ObservationTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $observation = new Observation([1, 2], 3);

        static::assertEquals([1, 2], $observation->getFeatures());
        static::assertEquals(3, $observation->getOutcome());
    }
}
