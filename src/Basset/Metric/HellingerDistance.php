<?php

declare(strict_types=1);

namespace Basset\Metric;


/**
 * https://en.wikipedia.org/wiki/Hellinger_distance
 */
class HellingerDistance extends Metric implements VSMInterface, DistanceInterface
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

        $meanV1 = $this->math->mean(array_count_values(array_keys($a)));
        $meanV2 = $this->math->mean(array_count_values(array_keys($b)));

        $n = count($a);
        $sum = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $sum += pow(sqrt($a[$key]/$meanV1)-sqrt($b[$key]/$meanV2),2);
            }
        }


        return sqrt($sum) * (1/sqrt(2));

    }


}
