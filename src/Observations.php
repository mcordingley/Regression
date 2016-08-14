<?php

namespace mcordingley\Regression;

use InvalidArgumentException;

final class Observations
{
    /** @var array */
    private $observations = [];

    /**
     * @param array $features
     * @param array $outcomes
     * @return Observations
     */
    public static function fromArray(array $features, array $outcomes)
    {
        $observationCount = count($outcomes);

        if (count($features) !== $observationCount) {
            throw new InvalidArgumentException('Must have as many outcomes as observations.');
        }

        $observations = new self;

        for ($i = 0; $i < $observationCount; $i++) {
            $observations->add($features[$i], $outcomes[$i]);
        }

        return $observations;
    }

    /**
     * @param array $features
     * @param float $outcome
     */
    public function add(array $features, $outcome)
    {
        $this->addObservation(new Observation($features, $outcome));
    }

    /**
     * @param Observation $observation
     */
    public function addObservation(Observation $observation)
    {
        $this->observations[] = $observation;
    }

    /**
     * @return void
     */
    public function shuffle()
    {
        shuffle($this->observations);
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return array_map(function (Observation $observation) {
            return $observation->getFeatures();
        }, $this->observations);
    }

    /**
     * @return array
     */
    public function getOutcomes()
    {
        return array_map(function (Observation $observation) {
            return $observation->getOutcome();
        }, $this->observations);
    }
}
