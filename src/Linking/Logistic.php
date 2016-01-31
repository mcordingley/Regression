<?php

namespace mcordingley\Regression\Linking;

class Logistic implements LinkingInterface
{
    public function delinearize($value)
    {
        return 1.0 / (1.0 + exp(-$value));
    }

    public function linearize($value)
    {
        return -log(1.0 / $value - 1);
    }
}
