<?php

namespace MCordingley\Regression\Data;

interface Entry
{
    /**
     * @return array
     */
    public function getFeatures(): array;

    /**
     * @return float
     */
    public function getOutcome(): float;
}
