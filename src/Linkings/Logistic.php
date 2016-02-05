<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linkings;

use InvalidArgumentException;
use mcordingley\Regression\Gradient;
use mcordingley\Regression\Helpers;

/**
 * Logistic
 *
 * Linking implementation that transforms data into out and out of logistic
 * form.
 */
final class Logistic extends Linking implements Gradient
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
        $sumProduct = Helpers::sumProduct($coefficients->toArray(), $observations);
        $hypothesis = $this->delinearize($sumProduct);

        return ($outcome - $hypothesis) * $hypothesis * (1.0 - $hypothesis) * $observations[$i];
    }
}
