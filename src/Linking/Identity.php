<?php

namespace mcordingley\Regression\Linking;

use mcordingley\Regression\Linking;

class Identity implements Linking
{
    public function delinearize($value)
    {
        return $value;
    }
    
    public function linearize($value)
    {
        return $value;
    }
}