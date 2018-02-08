<?php

namespace Basset\Similarity;

/**
 * Bray, J. R. and J. T. Curtis. 1957. An ordination of upland forest communities of southern Wisconsin. 
 * Ecological Monographs 27:325-349.
 * http://84.89.132.1/~michael/stanford/maeb5.pdf
 */

class BrayCurtisDistance implements DistanceInterface
{

    /**
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function dist(array $A, array $B)
    {

        $num = 0;
        $denom = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num += abs($A[$key]-$B[$key]);
                $denom += ($A[$key]+$B[$key]);
            }
        }

        return $denom != 0 ? $num/$denom : 0;

    }


}
