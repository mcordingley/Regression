<?php

namespace MCordingley\Regression\Data;

use Countable;
use IteratorAggregate;

interface Collection extends Countable, IteratorAggregate
{
    /**
     * @return int
     */
    public function getFeatureCount(): int;

    /**
     * @return array
     */
    public function getFeatures(): array;

    /**
     * @param int $index
     * @return Entry
     */
    public function getObservation(int $index): Entry;

    /**
     * @return array
     */
    public function getOutcomes(): array;
}
