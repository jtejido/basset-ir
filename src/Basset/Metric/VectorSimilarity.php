<?php

namespace Basset\Metric;


class VectorSimilarity extends Metric implements SimilarityInterface
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
        $dotProduct = 0;

        $uniqueKeys = $this->getAllUniqueKeys($a, $b);
        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])) {
                $dotProduct += ($a[$key] * $b[$key]);
            }
        }

        return $dotProduct;
    }

}
