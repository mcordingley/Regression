<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linking;

final class Logistic implements LinkingInterface
{
    public function delinearize(float $value): float
    {
        return 1.0 / (1.0 + exp(-$value));
    }

    public function linearize(float $value): float
    {
        return -log(1.0 / $value - 1);
    }
}
