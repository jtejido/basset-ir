<?php


namespace Basset\Metric;

/**
 * Vector Product for Probabilistic Models
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

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

        if(empty($a) || empty($b)){
            throw new \InvalidArgumentException('Vector $' . (empty($a) ? 'a' : 'b') . ' is not an array');
        }
        
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
