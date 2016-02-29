<?php

use mcordingley\Regression\Linkings\Logistic;
use mcordingley\Regression\Observations;
use mcordingley\Regression\Predictor;
use mcordingley\Regression\RegressionAlgorithms\GradientDescent;

final class LogisticTest extends PHPUnit_Framework_TestCase
{
    private $observations;
    private $predictor;
    private $regression;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Data from http://statistics.ats.ucla.edu/stat/r/dae/logit.htm
        $this->observations = new Observations;

        // [GRE, GPA, Rank2, Rank3, Rank4]
        $this->observations->addObservation(0, [380, 3.61, 0, 1, 0]);
        $this->observations->addObservation(1, [660, 3.67, 0, 1, 0]);
        $this->observations->addObservation(1, [800, 4.00, 0, 0, 0]);
        $this->observations->addObservation(1, [640, 3.19, 0, 0, 1]);
        $this->observations->addObservation(0, [520, 2.93, 0, 0, 1]);
        $this->observations->addObservation(1, [760, 3.00, 1, 0, 0]);

        $linking = new Logistic;
        $this->regression = new GradientDescent($linking);
        $this->coefficients = $this->regression->regress($this->observations);

        $this->predictor = new Predictor($this->coefficients, $linking);
    }

    public function testPredict()
    {
        $this->assertEquals(0.308, round($this->predictor->predict([200, 3.39, 0, 0, 0]), 3));
    }
}
