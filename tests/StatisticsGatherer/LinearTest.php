<?php

namespace mcordingley\Regression\Tests\StatisticsGatherer;

use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor\Linear as LinearPredictor;
use mcordingley\Regression\StatisticsGatherer\Linear as LinearStatisticsGatherer;
use PHPUnit_Framework_TestCase;

class LinearTest extends PHPUnit_Framework_TestCase
{
    private static $features = [
        [1, 1],
        [1, 2],
        [1, 1.3],
        [1, 3.75],
        [1, 2.25],
    ];

    private static $outcomes = [
        1,
        2,
        3,
        4,
        5,
    ];

    public function testStatistics()
    {
        $observations = Observations::fromArray(static::$features, static::$outcomes);
        $coefficients = [1.0954970633022, 0.92451598868827];
        $predictor = new LinearPredictor($coefficients);

        $statisticsGatherer = new LinearStatisticsGatherer(
            $observations,
            $coefficients,
            $predictor
        );

        static::assertEquals(4, $statisticsGatherer->getDegreesOfFreedomTotal());
        static::assertEquals(3, $statisticsGatherer->getDegreesOfFreedomError());
        static::assertEquals(1, $statisticsGatherer->getDegreesOfFreedomModel());
        static::assertEquals(1.94, round($statisticsGatherer->getFStatistic(), 2));
        static::assertEquals(0.39, round($statisticsGatherer->getRSquared(), 2));

        $stdErrorCoefficients = $statisticsGatherer->getStandardErrorCoefficients();
        static::assertEquals(1.51, round($stdErrorCoefficients[0], 2));
        static::assertEquals(0.66, round($stdErrorCoefficients[1], 2));
        static::assertEquals(1.42, round($statisticsGatherer->getStandardError(), 2));

        $tStatistics = $statisticsGatherer->getTStatistics();
        static::assertEquals(0.73, round($tStatistics[0], 2));
        static::assertEquals(1.39, round($tStatistics[1], 2));
    }
}