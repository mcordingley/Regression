<?php

declare(strict_types=1);

namespace mcordingley\Regression\RegressionAlgorithm;

use InvalidArgumentException;
use mcordingley\LinearAlgebra\Matrix;
use mcordingley\Regression\CoefficientSet;
use mcordingley\Regression\DataBag;
use mcordingley\Regression\RegressionAlgorithm;

final class LinearLeastSquares implements RegressionAlgorithm
{
    public function regress(DataBag $data): CoefficientSet
    {
        $dependentData = $data->getDependents();
        $independentData = $data->getIndependents();
        
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
        return new CoefficientSet($prediction->transpose()->toArray()[0]);
    }
}