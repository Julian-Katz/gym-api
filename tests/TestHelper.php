<?php

namespace Tests;

class TestHelper
{
    public static function arrays_have_same_order($array1, $array2) {
        // Check if both arrays have the same length
        if (count($array1) != count($array2)) {
            return false;
        }

        // Check if both arrays have the same elements in the same order
        for ($i = 0; $i < count($array1); $i++) {
            if ($array1[$i] != $array2[$i]) {
                return false;
            }
        }
        return true;
    }
}
