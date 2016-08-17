<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\Batch;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Observations;
use PHPUnit_Framework_TestCase;

/**
 * Puts the pieces together to show that a Logistic regression will converge. Note that the GRE feature has been
 * normalized. This leads to convergence in a matter of minutes, instead of hours/never.
 *
 * Note that this test can take a long time to execute and is therefore not included in the main test suite for CI.
 *
 * @package mcordingley\Regression\Tests
 */
class LogisticTest extends PHPUnit_Framework_TestCase
{
    public function testRegression()
    {
        $observations = $this->getLogisticObservations();
        $regression = new Batch(new LogisticGradient, new Fixed(0.125));

        // Example debug line for tuning the descent parameters.
        //$regression->addDescentIterationListener(new DescentLogger($gradient, $observations));

        static::assertEquals(
            [-3.98997907333, 0.2264425786179, 0.80403754928, -0.67544292796369, -1.340203916468, -1.5514636769182],
            $regression->regress($observations)
        );
    }

    /**
     * @return Observations
     */
    private function getLogisticObservations()
    {
        // Data from http://statistics.ats.ucla.edu/stat/r/dae/logit.htm
        $observations = new Observations;

        $csv = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'logistic.csv', 'r');
        fgetcsv($csv); // Throw away headers.

        while ($line = fgetcsv($csv)) {
            // Split composite feature, since the school rank isn't actually an interval value.
            $rank2 = $line[3] == 2 ? 1 : 0;
            $rank3 = $line[3] == 3 ? 1 : 0;
            $rank4 = $line[3] == 4 ? 1 : 0;

            // Normalize the GRE score. This is critical to get convergence.
            $gre = $line[1] / 100;

            //                 [1, GRE,  GPA,      Rank2,  Rank3,  Rank4],  Admitted
            $observations->add([1, $gre, $line[2], $rank2, $rank3, $rank4], (float) $line[0]);
        }

        fclose($csv);

        return $observations;
    }
}
