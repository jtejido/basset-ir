<?php

declare(strict_types=1);

namespace Basset\Metric;


/**
 * This is based on Sohangir and Wang's improved implementation of Zhu Et al.'s Square Root Cosine similarity.
 * Hellinger distance (L1 norm) is considerably more desirable than Euclidean distance (L2 norm) as a metric for 
 * high-dimensional applications.
 * DOI: 10.1109/ICCSE.2012.6295217 
 *
 * DOI 10.1186/s40537-017-0083-6
 * https://journalofbigdata.springeropen.com/track/pdf/10.1186/s40537-017-0083-6?site=journalofbigdata.springeropen.com
 *
 * Hubness is the dimensionality curse mentioned by Radovanovic Et al.:
 * 'On the existence of obstinate results in vector space models.'
 * DOI: 10.1145/1835449.1835482
 * Due to the concentration of distance in high-dimensional spaces, the ratio of the 
 * distances of the nearest and farthest neighbors to a given target is almost one.
 */
class SqrtCosineSimilarity extends Metric implements VSMInterface, SimilarityInterface
{


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  array $a
     * @param  array $b
     * @return float
     */
    public function similarity(array $a, array $b): float
    {

        $uniqueKeys = $this->getAllUniqueKeys($a, $b);
        $prod = 0;
        $v1_norm = 0;
        $v2_norm = 0;
        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $prod += sqrt($a[$key] * $b[$key]);
            }
            if (!empty($a[$key])) {
                $v1_norm += sqrt($a[$key]) * sqrt($a[$key]);
            }
            if (!empty($b[$key])) {
                $v2_norm += sqrt($b[$key]) * sqrt($b[$key]);
            }
        }
        $v1_norm = sqrt($v1_norm);
        $v2_norm = sqrt($v2_norm);

        if ($v1_norm==0 || $v2_norm==0){
            return 0;
        }

        return $prod/($v1_norm * $v2_norm);
    }

}
