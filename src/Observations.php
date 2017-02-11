<?php

declare(strict_types = 1);

namespace MCordingley\Regression;

use ArrayIterator;
use InvalidArgumentException;
use MCordingley\Regression\Data\Collection;
use MCordingley\Regression\Data\Entry;
use Traversable;

final class Observations implements Collection
{
    /** @var int */
    private $featureCount = 0;

    /** @var array */
    private $observations = [];

    /**
     * @param array $features
     * @param array $outcomes
     * @return self
     */
    public static function fromArray(array $features, array $outcomes): self
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
     * @return self
     */
    public function add(array $features, float $outcome): self
    {
        return $this->addObservation(new Observation($features, $outcome));
    }

    /**
     * @param Observation $observation
     * @return self
     */
    public function addObservation(Observation $observation): self
    {
        $featureCount = count($observation->getFeatures());

        if (!$this->featureCount) {
            $this->featureCount = $featureCount;
        } elseif ($this->featureCount !== $featureCount) {
            throw new InvalidArgumentException('All observations must have the same number of features.');
        }

        $this->observations[] = $observation;

        return $this;
    }

    /**
     * @return array
     */
    public function getFeatures(): array
    {
        return array_map(function (Observation $observation) {
            return $observation->getFeatures();
        }, $this->observations);
    }

    /**
     * @return array
     */
    public function getOutcomes(): array
    {
        return array_map(function (Observation $observation) {
            return $observation->getOutcome();
        }, $this->observations);
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->observations);
    }

    /**
     * @return int
     */
    public function getFeatureCount(): int
    {
        return $this->featureCount;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->observations);
    }

    /**
     * @param int $index
     * @return Entry
     */
    public function getObservation(int $index): Entry
    {
        return $this->observations[$index];
    }
}
