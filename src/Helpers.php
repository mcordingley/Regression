<?php

namespace mcordingley\Regression;

final class Helpers
{
    private function __construct() {}

    public static function sumProduct(...$arrays): float
    {
        $total = 0.0;

        for ($i = min(array_map('count', $arrays)); $i--; ) {
            $product = 1.0;

            foreach ($arrays as $array) {
                $product *= $array[$i];
            }

            $total += $product;
        }

        return $total;
    }
}
