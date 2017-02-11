<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm;

use InvalidArgumentException;
use MCordingley\LinearAlgebra\Matrix;
use MCordingley\Regression\Data\Collection;

final class LeastSquares implements Algorithm
{
    /**
     * @param Collection $observations
     * @return array
     * @throws InvalidArgumentException
     */
    public function regress(Collection $observations): array
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
