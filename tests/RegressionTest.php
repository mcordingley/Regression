<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor\Linear as LinearPredictor;
use mcordingley\Regression\Algorithm\LeastSquares;
use mcordingley\Regression\StatisticsGatherer\Linear as LinearStatisticsGatherer;
use PHPUnit_Framework_TestCase;

class RegressionTest extends PHPUnit_Framework_TestCase
{
    private $coefficients;
    private $observations;
    private $predictor;
    protected $regression;
    private $statisticsGatherer;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->observations = new Observations;

        $this->observations->add([1, 1], 1);
        $this->observations->add([1, 2], 2);
        $this->observations->add([1, 1.3], 3);
        $this->observations->add([1, 3.75], 4);
        $this->observations->add([1, 2.25], 5);

        $this->regression = new LeastSquares;
        $this->coefficients = $this->regression->regress($this->observations);

        $this->predictor = new LinearPredictor($this->coefficients);

        $this->statisticsGatherer = new LinearStatisticsGatherer(
            $this->observations,
            $this->coefficients,
            $this->predictor
        );
    }

    public function testCoefficients()
    {
        $this->assertEquals(1.095, round($this->coefficients[0], 3));
        $this->assertEquals(0.925, round($this->coefficients[1], 3));
    }

    public function testPredict()
    {
        $this->assertEquals(5.72, round($this->predictor->predict([1, 5]), 2));
    }

    public function testStatistics()
    {
        $this->assertEquals(3, $this->statisticsGatherer->getDegreesOfFreedomError());
        $this->assertEquals(1, $this->statisticsGatherer->getDegreesOfFreedomModel());
        $this->assertEquals(1.94, round($this->statisticsGatherer->getFStatistic(), 2));
        $this->assertEquals(0.39, round($this->statisticsGatherer->getRSquared(), 2));

        $stdErrorCoefficients = $this->statisticsGatherer->getStandardErrorCoefficients();
        $this->assertEquals(1.51, round($stdErrorCoefficients[0], 2));
        $this->assertEquals(0.66, round($stdErrorCoefficients[1], 2));
        $this->assertEquals(1.42, round($this->statisticsGatherer->getStandardError(), 2));

        $tStatistics = $this->statisticsGatherer->getTStatistics();
        $this->assertEquals(0.73, round($tStatistics[0], 2));
        $this->assertEquals(1.39, round($tStatistics[1], 2));
    }
}