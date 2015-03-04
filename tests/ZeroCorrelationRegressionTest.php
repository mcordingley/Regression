<?php

use mcordingley\Regression\Regression;
use mcordingley\Regression\RegressionAlgorithm\LinearLeastSquares;

class ZeroCorrelationRegressionTest extends PHPUnit_Framework_TestCase
{
    protected $regression;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->regression = new Regression(new LinearLeastSquares);

        $this->regression->addData(1, [1, 1]);
        $this->regression->addData(1, [1, 2]);
        $this->regression->addData(1, [1, 3]);
    }
    
    public function testCoefficients()
    {
        $coefficients = $this->regression->getCoefficients();
        
        $this->assertEquals(1.0, round($coefficients[0], 1));
        $this->assertEquals(0.0, round($coefficients[1], 1));
    }
    
    public function testPredict()
    {
        $this->assertEquals(1, round($this->regression->predict([1, 4])));
    }
    
    public function testRSquaredForZeroCorrelation()
    {
        $this->assertEquals(0.0, round($this->regression->getRSquared(), 1));
    }
}