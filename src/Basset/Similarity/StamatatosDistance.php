<?php

namespace Basset\Similarity;

/**
 * http://www.icsd.aegean.gr/lecturers/stamatatos/papers/survey.pdf
 */
class StamatatosDistance implements DistanceInterface
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
                $dist += pow(2 * ($A[$key]-$B[$key]) / ($A[$key]+$B[$key]), 2);
            }
        }

        return $dist;

    }


}
