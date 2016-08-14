<?php

namespace mcordingley\Regression\Algorithm;

use InvalidArgumentException;
use mcordingley\LinearAlgebra\Matrix;
use mcordingley\Regression\Observations;

final class LeastSquares implements Algorithm
{
    /** @var float */
    private $lambda;

    /**
     * @param float $lambda
     */
    public function __construct($lambda = 0.0)
    {
        $this->lambda = $lambda;
    }

    /**
     * @param Observations $data
     * @return array
     * @throws InvalidArgumentException
     */
    public function regress(Observations $data)
    {
        $design = new Matrix($data->getFeatures());
        $observed = (new Matrix([$data->getOutcomes()]))->transpose();

        if ($design->getRowCount() < $design->getColumnCount()) {
            throw new InvalidArgumentException('Not enough observations to perform regression. You need to have more observations than explanatory variables.');
        }

        $designTranspose = $design->transpose();

        $penalty = Matrix::identity($design->getColumnCount())->multiplyScalar($this->lambda);

        $prediction = $designTranspose
            ->multiplyMatrix($design)
            ->addMatrix($penalty)
            ->inverse()
            ->multiplyMatrix($designTranspose->multiplyMatrix($observed));

        return $prediction->transpose()->toArray()[0];
    }
}
