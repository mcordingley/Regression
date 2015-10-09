<?php

use mcordingley\Regression\Regression;
use mcordingley\Regression\RegressionAlgorithm\LinearLeastSquares;

class RegressionTest extends PHPUnit_Framework_TestCase
{
    protected $regression;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->regression = new Regression(new LinearLeastSquares);

        $this->regression->addData(1, [1, 1]);
        $this->regression->addData(2, [1, 2]);
        $this->regression->addData(3, [1, 1.3]);
        $this->regression->addData(4, [1, 3.75]);
        $this->regression->addData(5, [1, 2.25]);
    }
    
    public function testCoefficients()
    {
        $coefficients = $this->regression->getCoefficients();
        
        $this->assertEquals(1.095497063, round($coefficients[0], 9));
        $this->assertEquals(0.924515989, round($coefficients[1], 9));
    }
    
    public function testFStatistic()
    {
        $this->assertEquals(1.94, round($this->regression->getFStatistic(), 2));
    }
    
    public function testPredict()
    {
        $this->assertEquals(5.72, round($this->regression->predict([1, 5]), 2));
    }
    
    public function testRSquared()
    {
        $this->assertEquals(0.39, round($this->regression->getRSquared(), 2));
    }
}