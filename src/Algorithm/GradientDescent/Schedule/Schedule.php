<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Schedule;

interface Schedule
{
    /**
     * @param array $gradient
     * @return void
     */
    public function update(array $gradient);

    /**
     * @param int $featureIndex
     * @return float
     */
    public function step(int $featureIndex): float;
}
