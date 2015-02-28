<?php

namespace mcordingley\Regression\RegressionStrategy;

use mcordingley\LinearAlgebra\Matrix;
use mcordingley\RegressionStrategy;

class LinearLeastSquares implements RegressionStrategy
{
    public function regress(array $independentData, array $dependentData)
    {
        $design = new Matrix(array_map(function ($series) {
            return $series->getDesign();
        }, $independentData));
        
        $designTranspose = $design->transpose();
        $observed = (new Matrix([ $dependentData->getDesign() ]))->transpose();
        
        return $designTranspose->multiply($design)->inverse()->multiply($designTranspose)->multiply($observed)->transpose()->toArray();
    }
}