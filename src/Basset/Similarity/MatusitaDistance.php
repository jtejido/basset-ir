<?php

namespace Basset\Similarity;


/**
 * K. Matusita, Decision rules, based on the distance, for problems of fit, two
 * samples, and estimation, Ann. Math. Statist. 26 (1955) 631–640 
 */
class MatusitaDistance implements DistanceInterface
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
                $sum += pow(sqrt($A[$key])-sqrt($B[$key]),2);
            }
        }

        return sqrt($sum);

    }


}
