<?php

namespace mcordingley\Regression\Algorithm;

use InvalidArgumentException;
use mcordingley\LinearAlgebra\Matrix;
use mcordingley\Regression\Observations;

final class LeastSquares implements Algorithm
{
    /**
     * @param Observations $observations
     * @return array
     * @throws InvalidArgumentException
     */
    public function regress(Observations $observations)
    {
        $design = new Matrix($observations->getFeatures());
        $observed = (new Matrix([$observations->getOutcomes()]))->transpose();

        if ($design->getRowCount() < $design->getColumnCount()) {
            throw new InvalidArgumentException('Not enough observations to perform regression. You need to have more observations than explanatory variables.');
        }

        $designTranspose = $design->transpose();

        $prediction = $designTranspose
            ->multiplyMatrix($design)
            ->inverse()
            ->multiplyMatrix($designTranspose->multiplyMatrix($observed));

        return $prediction->transpose()->toArray()[0];
    }
}
