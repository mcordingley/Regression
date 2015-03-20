<?php

use mcordingley\Regression\SimpleRegression;

class PowerRegressionTest extends PHPUnit_Framework_TestCase
{
    protected $simple;
    
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->simple = SimpleRegression::makePowerRegression();
        
        // Experience points by CR of encounter from Pathfinder. ;)
        $this->simple->addData(400, 1)
                     ->addData(600, 2)
                     ->addData(800, 3)
                     ->addData(1200, 4)
                     ->addData(1600, 5)
                     ->addData(2400, 6)
                     ->addData(3200, 7)
                     ->addData(4800, 8)
                     ->addData(6400, 9)
                     ->addData(9600, 10)
                     ->addData(12800, 11)
                     ->addData(19200, 12)
                     ->addData(25600, 13)
                     ->addData(38400, 14)
                     ->addData(51200, 15)
                     ->addData(76800, 16)
                     ->addData(102400, 17)
                     ->addData(153600, 18)
                     ->addData(204800, 19)
                     ->addData(307200, 20)
                     ->addData(409600, 21)
                     ->addData(614400, 22)
                     ->addData(819200, 23)
                     ->addData(1228800, 24)
                     ->addData(1638400, 25);
    }
    
    public function testCoefficients()
    {
        $this->assertEquals(2.83, round($this->simple->getCoefficients()[0], 2));
    }
    
    public function testIntercept()
    {
        $this->assertEquals(37, round($this->simple->getIntercept(), 2));
    }
    
    public function testPredict()
    {
        $this->assertEquals(5.20, round($this->simple->predict([.5]), 2));
    }
}