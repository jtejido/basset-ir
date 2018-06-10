<?php

declare(strict_types=1);

namespace Basset\Metric;


/**
 * http://www.icsd.aegean.gr/lecturers/stamatatos/papers/survey.pdf
 */
class StamatatosDistance extends Metric implements VSMInterface, DistanceInterface
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
                $dist += pow(2 * ($a[$key] - $b[$key]) / ($a[$key] + $b[$key]), 2);
            }
        }

        return $dist;

    }


}
