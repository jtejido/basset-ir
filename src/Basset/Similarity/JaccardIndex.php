 <?php

namespace Basset\Similarity;

/**
 * http://en.wikipedia.org/wiki/Jaccard_index
 */
class JaccardIndex implements SimilarityInterface
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
        $union = count(array_fill_keys(array_merge($A,$B),1));

        return $intersect/$union;
    }

}
