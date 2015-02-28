<?php

use mcordingley\Regression\RegressionStrategy\LinearLeastSquares;

class LinearLeastSquaresTest extends PHPUnit_Framework_TestCase
{
    
    protected $strategy;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        $this->strategy = new LinearLeastSquares;
    }
    
    public function testFatMatrix()
    {
        $this->fail('Test not yet implemented.');
    }
    
    public function testSkinnyMatrix()
    {
        $coefficients = $this->strategy->regress([1, 2, 3, 4, 5], [[1, 1], [1, 2], [1, 1.3], [1, 3.75], [1, 2.25]]);
        
        $this->assertEquals(1.095497063, round($coefficients[0], 9));
        $this->assertEquals(0.924515989, round($coefficients[1], 9));
    }
    
    public function testSquareMatrix()
    {
        $this->fail('Test not yet implemented.');
    }
}