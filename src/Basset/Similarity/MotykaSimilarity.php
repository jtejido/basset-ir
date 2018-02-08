<?php

namespace Basset\Similarity;

/**
 * http://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf
 */
class MotykaSimilarity implements SimilarityInterface, DistanceInterface
{
    /**
    * The similarity returned by this algorithm is a number between 0,1
    */
    public function similarity(array $A, array $B)
    {

        $num = 0;
        $denom = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num += min($A[$key], $B[$key]);
                $denom += $A[$key] + $B[$key];
            }
        }


        return $denom != 0 ? $num/$denom : 0;
    }

    public function dist(array $A, array $B)
    {
        return 1-$this->similarity($A,$B);
    }
}