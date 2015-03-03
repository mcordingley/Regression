<?php

namespace mcordingley\Regression\RegressionAlgorithm;

use mcordingley\LinearAlgebra\Matrix;

class LinearLeastSquares implements RegressionAlgorithmInterface
{
    public function regress(array $dependentData, array $independentData)
    {
        $design = new Matrix($independentData);
        $observed = (new Matrix([ $dependentData ]))->transpose();
        
        // Use different math depending on the data dimensions:
        // http://math.stackexchange.com/questions/381600/singular-matrix-problem
        
        if ($design->columns == $design->rows) { // Square
            $prediction = $design->inverse()->multiply($observed);
        } elseif ($design->columns > $design->rows) { // Fat
            $designTranspose = $design->transpose();
            
            $prediction = $designTranspose->multiply(
                $design->multiply($designTranspose)
                       ->inverse()
            )->multiply($observed);
        } else { // Skinny
            $designTranspose = $design->transpose();
            
            $prediction = $designTranspose
                                 ->multiply($design)
                                 ->inverse()
                                 ->multiply($designTranspose->multiply($observed));
        }
        
        // Extract the vertical vector as a simple array.
        return $prediction->transpose()->toArray()[0];
    }
}