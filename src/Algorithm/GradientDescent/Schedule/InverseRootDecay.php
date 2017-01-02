<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * Decay schedule that divides the current gradient update by the nth root of the current iteration.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\Schedule
 */
final class InverseRootDecay implements Schedule
{
    /** @var int */
    private $iteration = 0;

    /** @var int */
    private $root;

    /** @var Schedule */
    private $schedule;

    /**
     * @param Schedule $schedule
     * @param int $root
     */
    public function __construct(Schedule $schedule, int $root = 2)
    {
        $this->root = $root;
        $this->schedule = $schedule;
    }

    /**
     * @param array $gradient
     * @return void
     */
    public function update(array $gradient)
    {
        $this->schedule->update($gradient);

        $this->iteration++;
    }

    /**
     * @param int $featureIndex
     * @return float
     */
    public function step(int $featureIndex): float
    {
        return $this->schedule->step($featureIndex) / pow($this->iteration, 1 / $this->root);
    }
}
