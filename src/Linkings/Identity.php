<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linkings;

use mcordingley\Regression\Helpers;

final class Identity extends Linking
{
    public function delinearize(float $value): float
    {
        return $value;
    }

    public function linearize(float $value): float
    {
        return $value;
    }

    public function loss(array $coefficients, array $observations, float $outcome, int $index): float
    {
        return -2 * ($outcome - Helpers::sumProduct($coefficients, $observations)) * $observations[$index];
    }
}
