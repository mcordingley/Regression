<?php

use mcordingley\Regression\SimpleRegression;

class SimpleRegressionTest extends PHPUnit_Framework_TestCase
{
    protected $simpleSkinnyRegression;

    public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);
        
        $this->simpleSkinnyRegression = new SimpleRegression;
        
        $this->simpleSkinnyRegression->addData(1, [ 1 ]);
        $this->simpleSkinnyRegression->addData(2, [ 2 ]);
        $this->simpleSkinnyRegression->addData(3, [ 1.3 ]);
        $this->simpleSkinnyRegression->addData(4, [ 3.75 ]);
        $this->simpleSkinnyRegression->addData(5, [ 2.25 ]);
    }
    
    public function testAssertIntercept()
    {
        $this->assertEquals(1.095497063, round($this->simpleSkinnyRegression->getIntercept(), 9));
    }
    
    public function testAssertCoefficients()
    {
        $coefficients = $this->simpleSkinnyRegression->getCoefficients();
        $this->assertEquals(0.924515989, round($coefficients[0], 9));
    }
}