<?php

namespace mcordingley\Regression\Tests\Algorithm\GradientDescent;

use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\DescentSpy;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;

/**
 * Helper trait for tests to output the results of each gradient descent iteration.
 *
 * @package mcordingley\Regression\Tests\Algorithm\GradientDescent
 */
trait DescentDebugger
{
    /**
     * @param StoppingCriteria $criteria
     * @return DescentSpy
     */
    private function getDescentSpy(StoppingCriteria $criteria)
    {
        $onIteration = function (array $gradient, array $coefficients) {
            $output = '[[' . implode(',', $gradient) . '], [' . implode(',', $coefficients) . ']]' . "\n";
            fwrite(STDERR, $output);
        };

        return new DescentSpy($criteria, $onIteration);
    }
}
