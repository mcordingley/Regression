<?php

declare(strict_types=1);

namespace mcordingley\Regression;

final class CoefficientSet
{
    private $coefficients;

    /**
     * __construct
     *
     * @param array $coefficients
     */
    public function __construct(array $coefficients)
    {
        $this->coefficients = $coefficients;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->coefficients;
    }
}
