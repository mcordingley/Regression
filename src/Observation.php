<?php

namespace mcordingley\Regression;

final class Observation
{
    /** @var array */
    private $features;

    /** @var float */
    private $outcome;

    /**
     * @param array $features
     * @param float $outcome
     */
    public function __construct(array $features, $outcome)
    {
        $this->features = $features;
        $this->outcome = $outcome;
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return float
     */
    public function getOutcome()
    {
        return $this->outcome;
    }
}
