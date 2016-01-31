<?php

declare(strict_types=1);

namespace mcordingley\Regression\RegressionAlgorithm;

use InvalidArgumentException;
use mcordingley\LinearAlgebra\Matrix;

final class LinearLeastSquares implements RegressionAlgorithmInterface
{
    public function regress(array $dependentData, array $independentData): array
    {
        $design = new Matrix($independentData);
        $observed = (new Matrix([ $dependentData ]))->transpose();
        
        if ($design->columns >= $design->rows) {
            throw new InvalidArgumentException('Not enough observations to perform regression. You need to have more observations than explanatory variables.');
        }
        
        $designTranspose = $design->transpose();

        $prediction = $designTranspose
                             ->multiply($design)
                             ->inverse()
                             ->multiply($designTranspose->multiply($observed));
        
        // Extract the vertical vector as a simple array.
        return $prediction->transpose()->toArray()[0];
    }
}