<?php

namespace MCordingley\Regression\Tests;

trait LeastSquaresFeatures
{
    /**
     * @return array
     */
    private function getFeatures()
    {
        return [
            [1, 1],
            [1, 2],
            [1, 1.3],
            [1, 3.75],
            [1, 2.25],
        ];
    }

    /**
     * @return array
     */
    private function getOutcomes()
    {
        return [
            1,
            2,
            3,
            4,
            5,
        ];
    }
}