Regression
==========

[![Build Status](https://api.travis-ci.org/repositories/mcordingley/Regression.svg)](https://travis-ci.org/mcordingley/Regression)

From [Wikipedia](http://en.wikipedia.org/wiki/Regression_analysis):

> In statistics, regression analysis is a statistical process for estimating the
> relationships among variables. It includes many techniques for modeling and
> analyzing several variables, when the focus is on the relationship between a
> dependent variable and one or more independent variables. More specifically,
> regression analysis helps one understand how the typical value of the dependent
> variable (or 'criterion variable') changes when any one of the independent
> variables is varied, while the other independent variables are held fixed.

Note: The library is largely stable and appears to be functioning well, but test coverage is still thin. Help this project reach 1.0 by using it and submitting pull requests with unit tests.

## Installation

Add this line to your composer.json file and update:

    "mcordingley/regression": "~0.9.4"

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
    $seCoefficients = $regression->getStandardErrorCoefficients();
    $tStatistics = $regression->getTStatistics();
    $fStatistic = $regression->getFStatistic();

You can also use the regression to predict values based on a new or hypothetical
set of explanatory data:

    $predictedOutcome = $regression->predict([1, 2, 3, 4, 5, 6]);

## Advanced Use

`SimpleRegression` is a facade (in the GoF sense) over the other classes in this
library. To make use of the more advanced features in this library, you'll need
to get your hands dirty with the constituent classes.

Start by instantiating an instance of `Regression`:

    use mcordingley\Regression\Regression;
    
    $regression = new Regression;

By default, the regression uses the Linear Least Squares method of estimating
the regression coefficients. If you have some alternate method that you would
like to use, pass an instance of your own class that implements
`mcordingley\Regression\RegressionAlgorithm\RegressionAlgorithmInterface` into
the `Regression` constructor as its only argument. If you think your new
algorithm would be of general use to others, *please submit it in a pull request.*

Next, apply any transformation logic to your data if data that you are using is
non-linear. This is done by injecting objects that implement the
`mcordingley\Regression\Linking\LinkingInterface` interface into the following methods:

    $regression->setDependentLinking($linking); // To transform Y values
    $regression->setIndependentLinking($linking); // To transform X values regardless of position in the data
    $regression->setIndependentLinking($linking, $index); // To transform the X values that are at $index position in the data in preference to the linking set above

Currently, these linking objects ship with the library:

- `mcordingley\Regression\Linking\Identity`: Passes data through untransformed. Used by default unless something else is specified.
- `mcordingley\Regression\Linking\Power`: For data that follows a geometric progression. Constructor takes the exponent as its argument. Default 2.
- `mcordingley\Regression\Linking\Exponential`: For data that follows an exponential progression. Constructor takes the base of the exponent as its argument. Default M_E.
- `mcordingley\Regression\Linking\Logarithm`: For data that follows a logarithmic progression. Constructor takes the base of the logarithm as its argument. Default M_E.

Depending on your data, other transformations may be appropriate. If you end up
developing a transform for use with your data that is not included already here
and that you think would be of use to others, *please submit it in a pull request*.

Adding data and pulling out the coefficients is also slightly different than
with the `SimpleRegression` object. The idea of an intercept is an abstraction
that is introduced by that object. When dealing with the data and computed
coefficients, you have to introduce a constant `1` as your first data point. This
represents the idea of invariance as a predictive "variable". Your first
computed coefficient is therefore your intercept. If this is still confusing,
take a look at the implementation of `SimpleRegression` to see how it abstracts
this out for you. The base `Regression` class leaves altering this up to you,
so you have the freedom to deviate from how `SimpleRegression` implements this
if necessary.
    
    $regression->addData(2, [1, 3, 5, 7, 2, 8, 10])
               ->addData(4, [1, 3, 2, 1, 5, 5, 9])
               ->addData(6, [1, 1, 2, 3, 4, 5, 6])
               ->addData(8, [1, 1, 3, 4, 7, 7, 12])
               ->addData(10, [1, 19, 17, 15, 14, 5, 1]);

As such, the `Regression` class does not have the `getIntercept` method that
`SimpleRegression` has. Otherwise the remaining methods operate the same, albeit
without stripping off the first value from any array return values:

    $coefficients = $regression->getCoefficients();
    $rSquared = $regression->getRSquared();
    $seCoefficients = $regression->getStandardErrorCoefficients();
    $tStatistics = $regression->getTStatistics();
    $fStatistic = $regression->getFStatistic();

    $predictedOutcome = $regression->predict([1, 1, 2, 3, 4, 5, 6]); // Note added 1 at the start

## Prerequisites For 1.0

- Get more tests in place. An adventurous early adopter of this library could
  run real-world data through an external tool to generate test values to put
  into unit tests here.
