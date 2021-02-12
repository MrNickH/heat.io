<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 16/07/2016
 * Time: 20:23
 */

class SortingFunctions
{
    public static $functionName;

    public static function FunctionSort($functionString, $array)
    {
        SortingFunctions::$functionName = $functionString;
        usort($array, "FunctionSortHelper");
        return $array;
    }
}

function FunctionSortHelper($Object1, $Object2)
{
    $funcName = SortingFunctions::$functionName;
    $val1 = $Object1->$funcName();
    $val2 = $Object2->$funcName();

    if ($val1 == $val2) {
        return 0;
    }
    if ($val1 < $val2) {
        return -1;
    }
    if ($val1 > $val2) {
        return 1;
    }
}