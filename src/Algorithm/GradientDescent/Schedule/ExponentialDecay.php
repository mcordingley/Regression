<?php

declare(strict_types = 1);

namespace MCordingley\Regression\Algorithm\GradientDescent\Schedule;

/**
 * Exponentially decays the learning rate to be `1 / $factor` after `$scale` iterations have passed.
 *
 * @package MCordingley\Regression\Algorithm\GradientDescent\Schedule
 */
final class ExponentialDecay implements Schedule
{
    /** @var int */
    private $iteration = 0;

    /** @var float */
    private $logFactor;

    /** @var int */
    private $scale;

    /** @var Schedule */
    private $schedule;

    /**
     * @param Schedule $schedule
     * @param int $scale
     * @param int $factor
     */
    public function __construct(Schedule $schedule, int $scale, int $factor = 1000)
    {
        $this->scale = $scale;
        $this->schedule = $schedule;
        $this->logFactor = log($factor);
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
        return $this->schedule->step($featureIndex) * exp(-$this->logFactor * $this->iteration / $this->scale);
    }
}
