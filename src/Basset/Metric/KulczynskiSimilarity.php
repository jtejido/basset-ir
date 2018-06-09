<?php

namespace Basset\Metric;


/**
 * http://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf
 */
class KulczynskiSimilarity extends Metric implements VSMInterface, SimilarityInterface
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

        $num = 0;
        $denom = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $num += abs($b[$key] - $a[$key]);
                $denom += min($b[$key], $a[$key]);
            }
        }


        return ($denom > 0) ? $num/$denom : 0;
    }

}