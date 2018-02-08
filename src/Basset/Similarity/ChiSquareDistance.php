<?php

namespace Basset\Similarity;

/**
 * http://www.itl.nist.gov/div898/handbook/eda/section3/eda35f.htm
 * The formula appeas assymetric so we'll just change it to be symmetric to both sets
 */
class ChiSquareDistance implements DistanceInterface
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
                $sum += ($A[$key]-$B[$key]) * ($A[$key]-$B[$key]) / ($A[$key]+$B[$key]) ;
            }
        }

        return $sum;

    }


}
