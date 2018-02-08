<?php

namespace Basset\Similarity;


/**
 * https://arxiv.org/pdf/0802.4376.pdf
 */
class LorentzianDistance implements DistanceInterface
{

    /**
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function dist(array $A, array $B)
    {

        $dist = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $dist += log(abs($A[$key]-$B[$key]) + 1);
            }
        }


        return $dist;

    }


}
