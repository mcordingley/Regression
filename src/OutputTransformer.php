<?php

declare(strict_types=1);

namespace mcordingley\Regression;

interface OutputTransformer
{
    public function delinearize(float $data): float;
}
