<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\Batch;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Algorithm\GradientDescent\StoppingCriteria\GradientNorm;
use mcordingley\Regression\Observations;
use PHPUnit_Framework_TestCase;

/**
 * Puts the pieces together to show that a Logistic regression will converge. Note that the GRE feature has been
 * normalized. This leads to convergence in a matter of minutes, instead of hours/never.
 */
class LogisticTest extends PHPUnit_Framework_TestCase
{
    /**
     * @large
     */
    public function testRegression()
    {
        $regression = new Batch(new LogisticGradient, new Fixed(0.125), new GradientNorm);
        $observations = $this->getLogisticObservations();

        static::assertEquals(
            [-3.9572690927850793, 0.22579298444865589, 0.79626535291848777, -0.67784339995776333, -1.3416834110939926, -1.55412650298527],
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
