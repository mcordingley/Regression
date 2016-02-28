<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linkings;

use InvalidArgumentException;
use mcordingley\Regression\Helpers;

final class Logistic extends Linking
{
    public function delinearize(float $value): float
    {
        return 1.0 / (1.0 + exp(-$value));
    }

    public function linearize(float $value): float
    {
        if ($value <= 0 || $value >= 1) {
            throw new InvalidArgumentException('Unable to linearize values outside of the range (0, 1).');
        }

        return -log(1.0 / $value - 1.0);
    }

    public function loss(array $coefficients, array $observations, float $outcome, int $index): float
    {
        $hypothesis = $this->delinearize(Helpers::sumProduct($coefficients, $observations));
        $error = $outcome - $hypothesis;

        // 0.5 will maximize the gradient, since we're on the wrong side.
        if ($error < -0.5 || $error > 0.5) {
            $hypothesis = 0.5;
        }

        return -2 * $error * $hypothesis * (1.0 - $hypothesis) * $observations[$index];
    }
}
