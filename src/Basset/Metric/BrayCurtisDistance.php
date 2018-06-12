<?php

declare(strict_types=1);

namespace Basset\Metric;


/**
 * Bray, J. R. and J. T. Curtis. 1957. An ordination of upland forest communities of southern Wisconsin. 
 * Ecological Monographs 27:325-349.
 *
 * @see http://84.89.132.1/~michael/stanford/maeb5.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class BrayCurtisDistance extends Metric implements VSMInterface, DistanceInterface
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


        $num = 0;
        $denom = 0;

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $num +=  abs($a[$key] - $b[$key]);
                $denom += ($a[$key] + $b[$key]);
            }
        }

        return $denom != 0 ? $num/$denom : 0;

    }


}
