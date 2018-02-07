<?php

namespace Basset\Similarity;


/**
 * This is based on Sohangir and Wang's improved implementation of Zhu Et al.'s Square Root Cosine similarity.
 * Hellinger distance (L1 norm) is considerably more desirable than Euclidean distance (L2 norm) as a metric for 
 * high-dimensional applications. 
 * DOI 10.1186/s40537-017-0083-6
 * https://journalofbigdata.springeropen.com/track/pdf/10.1186/s40537-017-0083-6?site=journalofbigdata.springeropen.com
 * Hubness is the dimensionality cursed mentioned by Radovanovic Et al.:
 * 'On the existence of obstinate results in vector space models.'
 * DOI: 10.1145/1835449.1835482
 * Due to the concentration of distance in high-dimensional spaces, the ratio of the 
 * distances of the nearest and farthest neighbors to a given target is almost one.
 */
class SqrtCosineSimilarity implements SimilarityInterface, DistanceInterface
{

    /**
     * @param  array $A Either feature vector or simply vector
     * @param  array $B Either feature vector or simply vector
     * @return float 
     */
    public function similarity(array $A, array $B)
    {


        $v1 = $A;
        $v2 = $B;
        $prod = 0;
        
        $v1_norm = 0;
        foreach ($v1 as $i=>$xi) {
            if(isset($v2[$i])){
                $prod += sqrt($xi*$v2[$i]);
                $v1_norm += sqrt($xi) * sqrt($xi);
            }
        }
        $v1_norm = sqrt($v1_norm);

        if ($v1_norm==0){
            return 0;
        }

        $v2_norm = 0;
        foreach ($v2 as $i=>$xi) {
            $v2_norm += sqrt($xi) * sqrt($xi);
        }
        $v2_norm = sqrt($v2_norm);

        if ($v2_norm==0){
            return 0;
        }

        return $prod/($v1_norm * $v2_norm);
    }

    public function dist(array $A, array $B)
    {
        return 1-$this->similarity($A,$B);
    }
}
