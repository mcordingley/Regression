<?php

declare(strict_types = 1);

namespace MCordingley\Regression\StatisticsGatherer;

use MCordingley\LinearAlgebra\Matrix;
use MCordingley\Regression\Data\Collection;
use MCordingley\Regression\Predictor\Predictor;

final class Linear
{
    // Great reference for most of these members:
    // http://facweb.cs.depaul.edu/sjost/csc423/documents/f-test-reg.htm

    /**
     * @var array
     */
    private $coefficients;

    /**
     * @var Collection
     */
    private $observations;

    /**
     * @var Predictor
     */
    private $predictor;

    /**
     * @var array
     */
    private $predictedOutcomes;

    /**
     * This is an array of the standard errors for each calculated coefficient.
     *
     * @var array
     */
    private $SCoefficients;

    /**
     * This is the sum of squared distances of observations from their
     * predicted values, a raw measure of the regression's overall error.
     *
     * @var float
     */
    private $sumSquaredError;

    /**
     * The sum of the squared distances of the predicted observations from the
     * mean of the true observations, a raw measure of the regression's overall
     * explanatory power.
     *
     * @var float
     */
    private $sumSquaredModel;

    /**
     * The sum of the squared distances of the observations from their mean.
     * SST = SSE + SSM Useful measure to put the other two sum of squares measures
     * into context
     *
     * @var float
     */
    private $sumSquaredTotal;

    /**
     * @var array
     */
    private $tStatistics;

    /**
     * @param Collection $observations
     * @param array $coefficients
     * @param Predictor $predictor
     */
    public function __construct(Collection $observations, array $coefficients, Predictor $predictor)
    {
        $this->observations = $observations;
        $this->coefficients = $coefficients;
        $this->predictor = $predictor;
    }

    /**
     * @return int
     */
    public function getDegreesOfFreedomTotal(): int
    {
        return count($this->observations) - 1;
    }

    /**
     * Returns the F statistic, which is compared against the F distribution CDF
     * to determine if the regression is "significant" or not.
     *
     * @return float
     */
    public function getFStatistic(): float
    {
        return $this->getMeanSquaredModel() / $this->getMeanSquaredError();
    }

    /**
     * Returns the mean-squared model of the regression, which is effectively
     * the "average" of the corresponding sum of squares.
     *
     * @return float
     */
    private function getMeanSquaredModel(): float
    {
        return $this->getSumSquaredModel() / $this->getDegreesOfFreedomModel();
    }

    /**
     * Calculates the sum-squared error of the regression. This is the sum
     * of the squared distances of predicted values from their average.
     *
     * @return float
     */
    private function getSumSquaredModel(): float
    {
        if (is_null($this->sumSquaredModel)) {
            $average = array_sum($this->observations->getOutcomes()) / count($this->observations);

            $this->sumSquaredModel = static::sumSquaredDifference($this->getPredictedOutcomes(), $average);
        }

        return $this->sumSquaredModel;
    }

    /**
     * @param array $series
     * @param float $baseline
     * @return float
     */
    private static function sumSquaredDifference(array $series, $baseline): float
    {
        return (float) array_sum(array_map(function ($element) use ($baseline) {
            return pow($element - $baseline, 2);
        }, $series));
    }

    /**
     * @return array
     */
    private function getPredictedOutcomes(): array
    {
        if (!$this->predictedOutcomes) {
            $this->predictedOutcomes = [];

            foreach ($this->observations->getFeatures() as $observed) {
                $this->predictedOutcomes[] = $this->predictor->predict($observed);
            }
        }

        return $this->predictedOutcomes;
    }

    /**
     * @return int
     */
    public function getDegreesOfFreedomModel(): int
    {
        return $this->observations->getFeatureCount() - 1;
    }

    /**
     * Returns the mean-squared error of the regression, which is effectively
     * the "average" of the corresponding sum of squares.
     *
     * @return float
     */
    private function getMeanSquaredError(): float
    {
        return $this->getSumSquaredError() / $this->getDegreesOfFreedomError();
    }

    /**
     * Calculates the sum of the squares of the residuals, which are the
     * distances of the observations from their predicted values, a raw measure
     * of the overall error in the regression model.
     *
     * @return float
     */
    private function getSumSquaredError(): float
    {
        if (is_null($this->sumSquaredError)) {
            $this->sumSquaredError = array_sum(array_map(function ($predicted, $observed) {
                return pow($predicted - $observed, 2);
            }, $this->getPredictedOutcomes(), $this->observations->getOutcomes()));
        }

        return $this->sumSquaredError;
    }

    /**
     * @return int
     */
    public function getDegreesOfFreedomError(): int
    {
        return count($this->observations) - $this->observations->getFeatureCount();
    }

    /**
     * Calculates the coefficient of determination. i.e. how well the line of
     * best fit describes the data.
     *
     * @return float
     */
    public function getRSquared(): float
    {
        $sumSquaredTotal = $this->getSumSquaredTotal();

        return $sumSquaredTotal ? 1 - $this->getSumSquaredError() / $sumSquaredTotal : 0.0;
    }

    /**
     * Calculates the sum-squared total of the regression. This is the sum
     * of the squared distances of observations from their average, a useful
     * measure to put the sum-squared error (SSE) and sum-squared model (SSM)
     * into context.
     *
     * @return float
     */
    private function getSumSquaredTotal(): float
    {
        if (is_null($this->sumSquaredTotal)) {
            $average = array_sum($this->observations->getOutcomes()) / count($this->observations);

            $this->sumSquaredTotal = static::sumSquaredDifference($this->observations->getOutcomes(), $average);
        }

        return $this->sumSquaredTotal;
    }

    /**
     * Calculates the standard error of the regression. This is the average
     * distance of observed values from the regression line. It's conceptually
     * similar to the standard deviation.
     *
     * @return float
     */
    public function getStandardError(): float
    {
        return sqrt($this->getMeanSquaredError());
    }

    /**
     * Calculates the t test values of each of the regression coefficients.
     *
     * @return array
     */
    public function getTStatistics(): array
    {
        if (is_null($this->tStatistics)) {
            $this->tStatistics = array_map(function ($predictor, $SCoefficient) {
                return $predictor / $SCoefficient;
            }, $this->coefficients, $this->getStandardErrorCoefficients());
        }

        return $this->tStatistics;
    }

    /**
     * Calculates the standard error of each of the regression coefficients.
     *
     * @return array
     */
    public function getStandardErrorCoefficients(): array
    {
        if (is_null($this->SCoefficients)) {
            $design = new Matrix($this->observations->getFeatures());

            $inverted = $design->transpose()
                ->multiplyMatrix($design)
                ->inverse();

            $diagonalVector = [];

            for ($i = 0, $size = $inverted->getRowCount(); $i < $size; $i++) {
                $diagonalVector[] = $inverted->get($i, $i);
            }

            $this->SCoefficients = (new Matrix([$diagonalVector]))
                    ->multiplyScalar($this->getMeanSquaredError())
                    ->map(function ($element) {
                        return sqrt($element);
                    })
                    ->toArray()[0];
        }

        return $this->SCoefficients;
    }
}
