<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent;

use MCordingley\Regression\Data\Collection;

final class Stochastic extends GradientDescent
{
    /**
     * @param Collection $observations
     * @param array $coefficients
     * @return array
     */
    protected function calculateGradient(Collection $observations, array $coefficients): array
    {
        $observation = $observations->getObservation(mt_rand(0, $observations->count() - 1));

        return $this->gradient->gradient($coefficients, $observation->getFeatures(), $observation->getOutcome());
    }
}
