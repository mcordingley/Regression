<?php

use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor;
use mcordingley\Regression\RegressionAlgorithms\LinearLeastSquares;
use mcordingley\Regression\StatisticsGatherer;

final class RegressionTest extends PHPUnit_Framework_TestCase
{
    private $observations;
    private $predictor;
    private $regression;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->observations = new Observations;

        $this->observations->addObservation(1, [1]);
        $this->observations->addObservation(2, [2]);
        $this->observations->addObservation(3, [1.3]);
        $this->observations->addObservation(4, [3.75]);
        $this->observations->addObservation(5, [2.25]);
        
        $this->regression = new LinearLeastSquares;
        $this->coefficients = $this->regression->regress($this->observations);
        
        $this->predictor = new Predictor($this->coefficients);
        $this->statisticsGatherer = new StatisticsGatherer($this->observations, $this->coefficients, $this->predictor);
    }
    
    public function testCoefficients()
    {
        $this->assertEquals(1.095497063, round($this->coefficients[0], 9));
        $this->assertEquals(0.924515989, round($this->coefficients[1], 9));
    }
    
    public function testPredict()
    {
        $this->assertEquals(5.72, round($this->predictor->predict([5]), 2));
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
    }
}