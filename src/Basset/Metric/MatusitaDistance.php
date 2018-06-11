<?php

declare(strict_types=1);

namespace Basset\Metric;


/**
 * @see K. Matusita, Decision rules, based on the distance, for problems of fit, two
 * samples, and estimation, Ann. Math. Statist. 26 (1955) 631–640 
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class MatusitaDistance extends Metric implements VSMInterface, DistanceInterface
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
                $sum += pow(sqrt($a[$key])-sqrt($b[$key]),2);
            }
        }

        return sqrt($sum);

    }


}
