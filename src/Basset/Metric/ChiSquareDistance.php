<?php

namespace Basset\Metric;


/**
 * http://www.itl.nist.gov/div898/handbook/eda/section3/eda35f.htm
 * The formula appears assymetric so we'll just change it to be symmetric to both sets
 */
class ChiSquareDistance extends Metric implements VSMInterface, DistanceInterface
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

        $sum = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $num = ($a[$key] - $b[$key]) * ($a[$key] - $b[$key]);
                $denom = ($a[$key] + $b[$key]);
                $sum += ($denom > 0) ? $num/$denom : 0  ;
            }
        }

        return $sum;

    }


}
