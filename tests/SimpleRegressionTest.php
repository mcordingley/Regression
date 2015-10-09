<?php

use mcordingley\Regression\SimpleRegression;

class SimpleRegressionTest extends PHPUnit_Framework_TestCase
{
    protected $simple;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->simple = new Simpleregression;

        $this->simple->addData(1, [1]);
        $this->simple->addData(2, [2]);
        $this->simple->addData(3, [1.3]);
        $this->simple->addData(4, [3.75]);
        $this->simple->addData(5, [2.25]);
    }
    
    public function testCoefficients()
    {
        $this->assertEquals(0.924515989, round($this->simple->getCoefficients()[0], 9));
    }
    
    public function testFStatistic()
    {
        $this->assertEquals(1.94, round($this->simple->getFStatistic(), 2));
    }
    
    public function testIntercept()
    {
        $this->assertEquals(1.095497063, round($this->simple->getIntercept(), 9));
    }
    
    public function testPredict()
    {
        $this->assertEquals(5.72, round($this->simple->predict([5]), 2));
    }
    
    public function testRSquared()
    {
        $this->assertEquals(0.39, round($this->simple->getRSquared(), 2));
    }
}