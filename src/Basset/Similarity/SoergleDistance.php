<?php

namespace Basset\Similarity;

/**
 * http://www.orgchm.bas.bg/~vmonev/SimSearch.pdf
 */
class SoergleDistance implements DistanceInterface
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
                $denom += max($A[$key], $B[$key]);
            }
        }

        return $denom != 0 ? $num/$denom : 0;

    }


}
