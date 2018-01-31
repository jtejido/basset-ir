<?php

namespace Basset\Similarity;

use Basset\Math\Math;

class Euclidean implements SimilarityInterface, DistanceInterface
{

    /**
     * This class computes the very simple euclidean distance between
     * two vectors ( sqrt(sum((a_i-b_i)^2)) ).
     * @param  array $A Either a vector or a collection of tokens to be transformed to a vector
     * @param  array $B Either a vector or a collection of tokens to be transformed to a vector
     * @return float The euclidean distance between $A and $B
     */
    public function similarity(array $A, array $B)
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

    public function dist(array $A, array $B)
    {
        return 1-$this->similarity($A,$B);
    }
}
