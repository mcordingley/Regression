<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Observation;
use mcordingley\Regression\Observations;
use PHPUnit_Framework_TestCase;

class ObservationsTest extends PHPUnit_Framework_TestCase
{
    private static $features = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
        [10, 11, 12],
    ];

    private static $outcomes = [
        1,
        2,
        3,
        4,
    ];

    public function testObservationsFromArray()
    {
        $observations = Observations::fromArray(static::$features, static::$outcomes);

        static::assertEquals(3, $observations->getFeatureCount());
        static::assertEquals(static::$features, $observations->getFeatures());
        static::assertEquals(static::$outcomes, $observations->getOutcomes());
    }

    public function testObservationsFromBadArray()
    {
        static::setExpectedException('InvalidArgumentException');

        Observations::fromArray(static::$features, [1, 2, 3]);
    }

    public function testBadObservationsCount()
    {
        $observations = new Observations;
        $observations->add([1, 2, 3], 4);

        static::setExpectedException('InvalidArgumentException');

        $observations->add([1, 2], 4);
    }

    public function testGetIterator()
    {
        $observations = Observations::fromArray(static::$features, static::$outcomes);

        /**
         * @var int $i
         * @var Observation $observation
         */
        foreach ($observations as $i => $observation)
        {
            static::assertEquals(static::$features[$i], $observation->getFeatures());
        }
    }

    public function testGetObservation()
    {
        $observations = Observations::fromArray(static::$features, static::$outcomes);
        $observation = $observations->getObservation(0);

        static::assertEquals(static::$features[0], $observation->getFeatures());
        static::assertEquals(static::$outcomes[0], $observation->getOutcome());
    }
}