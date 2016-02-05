<?php

declare(strict_types=1);

namespace mcordingley\Regression\Linkings;

use mcordingley\Regression\OutputTransformer;

/**
 * Linking
 *
 * Inheriting classes are instances of linking functions as defined here:
 *
 * https://en.wikipedia.org/wiki/Generalized_linear_model#Link_function
 *
 * Each Linking object is a matched pair of the link and mean functions. Only
 * the mean function is used directly by this library. The link function is
 * provided for completeness and convenience.
 */
abstract class Linking implements OutputTransformer
{
    /**
     * delinearize
     *
     * Also known as the "Mean Function".
     *
     * @param float $value
     * @return float
     */
    abstract public function delinearize(float $value): float;

    /**
     * linearize
     *
     * Also known as the "Link Function".
     *
     * @param float $value
     * @return float
     */
    abstract public function linearize(float $value): float;
}
