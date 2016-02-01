<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linking;

use mcordingley\Regression\InputTransformer;
use mcordingley\Regression\OutputTransformer;

/**
 * Identity
 *
 * Default implementation of Linking that returns data untransformed. Used for
 * data that is already linear.
 */
final class Identity implements InputTransformer, OutputTransformer
{
    public function delinearize(float $value): float
    {
        return $value;
    }

    public function linearize(float $value): float
    {
        return $value;
    }
}
