Regression
==========

This is a regression package for PHP that performs multiple regression on numeric
data. It is still in active development, so the API may still shift, but it is
also finally in a usable state for probably most use cases. Use, but still with
caution.

## Installation

Add this line to your composer.json file and update:

    "mcordingley/regression": "dev-master"

That's it!

## Quick Start

For basic usage, add a `use` statement to import `SimpleRegression` into your
current namespace:

    use mcordingley\Regression\SimpleRegression;

Then instantiate a new instance of this and add data to it:

    $regression = new SimpleRegression;
    
    $regression->addData(2, [3, 5, 7, 2, 8, 10])
               ->addData(4, [3, 2, 1, 5, 5, 9])
               ->addData(6, [1, 2, 3, 4, 5, 6])
               ->addData(8, [1, 3, 4, 7, 7, 12])
               ->addData(10, [19, 17, 15, 14, 5, 1]);

The first argument to `addData` is the observed outcome. The second is an array of
explanatory data associated with that outcome. If performing a single regression
instead of a multiple regression, just supply a single value to the array.

After you're done adding data, you can get the intercept, the coefficients on the
explanatory data, and the R-squared value that shows how well the fitted trend
line matches the data:

    $intercept = $regression->getIntercept();
    $coefficients = $regression->getCoefficients();
    $rSquared = $regression->getRSquared();

You can also use the regression to predict values based on a new or hypothetical
set of explanatory data:

    $predictedOutcome = $regression->predict([1, 2, 3, 4, 5, 6]);

## Advanced Use

Coming soon!

## Check-list For First Release

- Finish tests for fat and square matrices on the Least Squares strategy.