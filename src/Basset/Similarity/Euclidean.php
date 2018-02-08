<?php

namespace Basset\Similarity;


class Euclidean implements DistanceInterface
{

    /**
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function dist(array $A, array $B)
    {

        $r = array();
        foreach ($A as $k=>$v) {
            $r[$k] = $v;
        }
        foreach ($B as $k=>$v) {
            if (isset($r[$k]))
                $r[$k] -= $v;
            else
                $r[$k] = $v;
        }

        return sqrt(
            array_sum(
                array_map(
                    function ($x) {
                        return $x*$x;
                    },
                    $r
                )
            )
        );

    }

}
