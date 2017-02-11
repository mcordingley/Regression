<?php

namespace MCordingley\Regression\Tests\Algorithm\GradientDescent;

use MCordingley\Regression\Algorithm\Algorithm;
use MCordingley\Regression\Data\Collection;
use MCordingley\Regression\Observations;
use PHPUnit_Framework_TestCase;

abstract class GradientDescent extends PHPUnit_Framework_TestCase
{
    /**
     * @large
     */
    public function testRegression()
    {
        $regression = $this->makeRegression();
        $observations = $this->getLogisticObservations();

        static::assertEquals($this->getExpectedCoefficients(), $regression->regress($observations));
    }

    /**
     * @return Algorithm
     */
    abstract protected function makeRegression();

    /**
     * @return array
     */
    abstract protected function getExpectedCoefficients();

    /**
     * @return Collection
     */
    private function getLogisticObservations(): Collection
    {
        // Data from http://statistics.ats.ucla.edu/stat/r/dae/logit.htm
        $observations = new Observations;

        $csv = fopen(__DIR__ . '/../../fixtures/logistic.csv', 'r');
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
