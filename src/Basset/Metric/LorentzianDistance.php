<?php

declare(strict_types=1);

namespace Basset\Metric;


/**
 * @see https://arxiv.org/pdf/0802.4376.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class LorentzianDistance extends Metric implements VSMInterface, DistanceInterface
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

        $dist = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $dist += log(abs($a[$key] - $b[$key]) + 1);
            }
        }


        return $dist;

    }


}
