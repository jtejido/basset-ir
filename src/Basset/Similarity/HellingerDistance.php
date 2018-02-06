<?php

namespace Basset\Similarity;

use Basset\Math\Math;

/**
 * https://en.wikipedia.org/wiki/Hellinger_distance
 */
class HellingerDistance implements DistanceInterface
{

    /**
     * @param  array $A Either feature vector or simply vector
     * @param  array $B Either feature vector or simply vector
     * @return float The cosinus of the angle between the two vectors
     */
    public function dist(array $A, array $B)
    {
        $math = new Math();
        $meanV1 = $math->mean($A);
        $meanV2 = $math->mean($B);

        $n = count($A);
        $sum = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $sum += pow(sqrt($A[$key]/$meanV1)-sqrt($B[$key]/$meanV2),2);
            }
        }


        return sqrt($sum) * (1/sqrt(2));

    }


}
