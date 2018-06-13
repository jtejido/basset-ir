<?php


namespace Basset\Metric;

use Basset\Math\Math;


class Metric
{

    protected $math;

    public function __construct()
    {
        $this->math = new Math();
    }

    public function getAllUniqueKeys(array $A, array $B)
    {
        $keysA = array_keys($A);
        $keysB = array_keys($B);
        return array_keys(array_count_values(array_merge($keysA, $keysB)));
    }


}
