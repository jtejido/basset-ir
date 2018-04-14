<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * Given two vectors compute cos(theta) where theta is the angle
 * between the two vectors in a N-dimensional vector space.
 *
 * cos(theta) = A•B / |A||B|
 */
class CosineSimilarity extends Similarity implements SimilarityInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns a number between 0,1 that corresponds to the cos(theta)
     * where theta is the angle between the two sets if they are treated
     * as n-dimensional vectors.
     *
     * @param  QueryDocument $q
     * @param  Document $doc
     * @return float
     */
    public function similarity(DocumentInterface $q, DocumentInterface $doc)
    {
        
        $A = $this->getTokens($q, true);
        $B = $this->getTokens($doc, true);

        $uniqueKeys = $this->getAllUniqueKeys($A, $B);
        $prod = 0;
        
        $v1_norm = 0;
        $v2_norm = 0;
        foreach ($uniqueKeys as $key) {

            if (!empty($A[$key]) && !empty($B[$key])){
                $prod += ($A[$key] * $B[$key]);
            }
            if (!empty($A[$key])) {
                $v1_norm += ($A[$key] * $A[$key]);
            }
            if (!empty($B[$key])) {
                $v2_norm += ($B[$key] * $B[$key]);
            }
        }

        $v1_norm = sqrt($v1_norm);
        $v2_norm = sqrt($v2_norm);

        if ($v1_norm ==0 || $v2_norm ==0){
            return 0;
        }

        return $prod/($v1_norm * $v2_norm);

    }

}
