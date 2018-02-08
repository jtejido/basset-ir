<?php

namespace Basset\Similarity;

/**
 * https://en.wikipedia.org/wiki/Chebyshev_distance
 * AKA Maximum Metric
 */
class ChebyshevDistance implements DistanceInterface
{

    /**
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function dist(array $A, array $B)
    {

        $max = 0;
        $aux = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $aux += abs($A[$key]-$B[$key]);
                if ($max < $aux) {
                    $max = $aux;
                }
            }
        }


        return $max;

    }


}
