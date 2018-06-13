<?php


namespace Basset\Metric;

/**
 * @see https://en.wikipedia.org/wiki/Kullback%E2%80%93Leibler_divergence
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class KLDivergence extends Metric implements VSMInterface, DistanceInterface
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
    public function dist(array $a, array $b): float
    {

        $uniqueKeys = $this->getAllUniqueKeys($a, $b);
        $klDiv = 0;
        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $klDiv += ($a[$key] > 0) ? ($b[$key] * log($b[$key]/$a[$key])) : 0;
            }
        }

        return $klDiv;

    }

}
