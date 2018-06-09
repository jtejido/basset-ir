<?php

namespace Basset\Metric;


/**
 * http://www.orgchm.bas.bg/~vmonev/SimSearch.pdf
 */
class SoergleDistance extends Metric implements VSMInterface, DistanceInterface
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

        $num = 0;
        $denom = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $num += abs($b[$key] - $a[$key]);
                $denom += max($b[$key], $a[$key]);
            }
        }

        return $denom > 0 ? $num/$denom : 0;

    }


}
