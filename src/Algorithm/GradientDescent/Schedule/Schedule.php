<?php

namespace mcordingley\Regression\Algorithm\GradientDescent\Schedule;

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
    public function step($featureIndex);
}
