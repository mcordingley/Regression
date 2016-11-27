<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent;

use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\DescentSpy;
use MCordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\StoppingCriteria;

/**
 * Helper trait for tests to output the results of each gradient descent iteration.
 *
 * @package MCordingley\Regression\Tests\Algorithm\GradientDescent
 */
trait DescentDebugger
{
    /**
     * @param StoppingCriteria $criteria
     * @return DescentSpy
     */
    private function getDescentSpy(StoppingCriteria $criteria)
    {
        return new DescentSpy($criteria, function (array $gradient, array $coefficients) {
            $output = '[[' . implode(',', $gradient) . '], [' . implode(',', $coefficients) . ']]' . "\n";
            fwrite(STDERR, $output);
        });
    }
}
