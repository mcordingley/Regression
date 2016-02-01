<?php

declare(strict_types=1);

namespace mcordingley\Regression;

use ArrayIterator;
use IteratorAggregate;

final class CoefficientSet implements IteratorAggregate
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

    public function getIterator()
    {
        return new ArrayIterator($this->coefficients);
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
