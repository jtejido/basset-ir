<?php

namespace Basset\Similarity;

/**
 * http://en.wikipedia.org/wiki/Sørensen–Dice_coefficient
 */
class DiceSimilarity implements SimilarityInterface
{
    /**
     * @param  array $A
     * @param  array $B
     * @return float
    */
    public function similarity(array $A, array $B)
    {


        $a = array_fill_keys($A,1);
        $b = array_fill_keys($B,1);

        $intersect = count(array_intersect_key($a,$b));
        $a_count = count($a);
        $b_count = count($b);

        return (2*$intersect)/($a_count + $b_count);
    }

}