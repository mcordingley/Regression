<?php

declare(strict_types=1);

namespace mcordingley\Regression;

final class Observations
{
    private $dependent = [];
    private $independent = [];

    /**
     * addObservation
     *
     * @param float $dependent The outcome of this observation.
     * @param array $independent The predictive variables for this observation, as floats.
     * @return self
     */
    public function addObservation(float $dependent, array $independent): self
    {
        $this->dependent[] = $dependent;

        $this->independent[] = array_merge([1], array_map(function($element) {
            return (float) $element;
        }, $independent));

        return $this;
    }

    /**
     * getDependents
     *
     * @return array All of the observed outcomes, in order of addition.
     */
    public function getDependents(): array
    {
        return $this->dependent;
    }

    /**
     * getIndependents
     *
     * @return array Array of arrays of the predictive variables, in order of addition.
     */
    public function getIndependents(): array
    {
        return $this->independent;
    }
}
