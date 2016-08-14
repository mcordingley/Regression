<?php

namespace mcordingley\Regression\Tests;

use mcordingley\Regression\Algorithm\GradientDescent\Batch;
use mcordingley\Regression\Algorithm\GradientDescent\Gradient\Logistic as LogisticGradient;
use mcordingley\Regression\Algorithm\GradientDescent\Schedule\Adagrad;
use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor\Logistic as LogisticPredictor;
use PHPUnit_Framework_TestCase;

final class LogisticTest extends PHPUnit_Framework_TestCase
{
    private $coefficients;
    private $observations;
    private $predictor;
    private $regression;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Data from http://statistics.ats.ucla.edu/stat/r/dae/logit.htm
        $this->observations = new Observations;

        $csv = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'logistic.csv', 'r');
        fgetcsv($csv); // Throw away headers.

        while ($line = fgetcsv($csv)) {
            $rank2 = $line[3] == 2 ? 1 : 0;
            $rank3 = $line[3] == 3 ? 1 : 0;
            $rank4 = $line[3] == 4 ? 1 : 0;

            //                       [GRE,      GPA,      Rank2,  Rank3,  Rank4]   Admitted
            $this->observations->add([$line[1], $line[2], $rank2, $rank3, $rank4], (float) $line[0]);
        }

        fclose($csv);

        $this->regression = new Batch(new LogisticGradient, new Adagrad);
        $this->coefficients = $this->regression->regress($this->observations);
        $this->predictor = new LogisticPredictor($this->coefficients);
    }

    public function testPredict()
    {
        $this->assertEquals(0.308, round($this->predictor->predict([200, 3.39, 0, 0, 0]), 3));
    }
}
