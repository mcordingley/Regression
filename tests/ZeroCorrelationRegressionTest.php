<?php

use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor;
use mcordingley\Regression\RegressionAlgorithms\LinearLeastSquares;
use mcordingley\Regression\StatisticsGatherer;

final class ZeroCorrelationRegressionTest extends PHPUnit_Framework_TestCase
{
    private $observations;
    private $predictor;
    private $regression;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->observations = new Observations;

        $this->observations->addObservation(1, [1]);
        $this->observations->addObservation(1, [2]);
        $this->observations->addObservation(1, [3]);
        
        $this->regression = new LinearLeastSquares;
        $this->coefficients = $this->regression->regress($this->observations);
        
        $this->predictor = new Predictor($this->coefficients);
        $this->statisticsGatherer = new StatisticsGatherer($this->observations, $this->coefficients, $this->predictor);
    }
    
    public function testCoefficients()
    {
        $this->assertEquals(1.0, round($this->coefficients[0], 1));
        $this->assertEquals(0.0, round($this->coefficients[1], 1));
    }
    
    public function testPredict()
    {
        $this->assertEquals(1, round($this->predictor->predict([4])));
    }
    
    public function testStatistics()
    {
        $this->assertEquals(0.0, round($this->statisticsGatherer->getRSquared(), 1));
    }
}