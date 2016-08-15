<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\Batch;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Fixed;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor\Logistic as LogisticPredictor;
use PHPUnit_Framework_TestCase;

final class LogisticTest extends PHPUnit_Framework_TestCase
{
    private $predictor;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Data from http://statistics.ats.ucla.edu/stat/r/dae/logit.htm
        $observations = new Observations;

        $csv = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'logistic.csv', 'r');
        fgetcsv($csv); // Throw away headers.

        while ($line = fgetcsv($csv)) {
            $rank2 = $line[3] == 2 ? 1 : 0;
            $rank3 = $line[3] == 3 ? 1 : 0;
            $rank4 = $line[3] == 4 ? 1 : 0;

            //                 [1, GRE,            GPA,      Rank2,  Rank3,  Rank4],  Admitted
            $observations->add([1, $line[1] / 100, $line[2], $rank2, $rank3, $rank4], (float) $line[0]);
        }

        fclose($csv);

        $regression = new Batch(new LogisticGradient, new Fixed(0.125));

        // Example debug line for tuning the descent parameters.
        //$regression->addDescentIterationListener(new DescentLogger);

        $coefficients = $regression->regress($observations);

        $this->predictor = new LogisticPredictor($coefficients);
    }

    public function testPredict()
    {
        $this->assertEquals(0.308, round($this->predictor->predict([1, 2, 3.39, 0, 0, 0]), 3));
    }
}
