<?php

namespace MCordingley\Regression\Tests\StatisticsGatherer;

use MCordingley\Regression\Observations;
use MCordingley\Regression\Predictor\Linear as LinearPredictor;
use MCordingley\Regression\StatisticsGatherer\Linear as LinearStatisticsGatherer;
use MCordingley\Regression\Tests\LeastSquaresFeatures;
use PHPUnit_Framework_TestCase;

class LinearTest extends PHPUnit_Framework_TestCase
{
    use LeastSquaresFeatures;

    public function testStatistics()
    {
        $observations = Observations::fromArray($this->getFeatures(), $this->getOutcomes());
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