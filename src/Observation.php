<?php

declare(strict_types = 1);

namespace MCordingley\Regression;

use MCordingley\Regression\Data\Entry;

final class Observation implements Entry
{
    /** @var array */
    private $features;

    /** @var float */
    private $outcome;

    /**
     * @param array $features
     * @param float $outcome
     */
    public function __construct(array $features, float $outcome)
    {
        $this->features = $features;
        $this->outcome = $outcome;
    }

    /**
     * @return array
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * @return float
     */
    public function getOutcome(): float
    {
        return $this->outcome;
    }
}
