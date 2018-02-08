<?php

namespace Basset\Similarity;

/**
 * https://en.wikipedia.org/wiki/Canberra_distance
 */
class CanberraDistance implements DistanceInterface
{

    /**
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function dist(array $A, array $B)
    {

        $sum = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $sum += abs($A[$key]-$B[$key]) / ($A[$key]+$B[$key]) ;
            }
        }

        return $sum;

    }


}
