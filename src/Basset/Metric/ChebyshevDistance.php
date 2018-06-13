<?php


namespace Basset\Metric;


/**
 * AKA Maximum Metric
 *
 * @see https://en.wikipedia.org/wiki/Chebyshev_distance
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class ChebyshevDistance extends Metric implements VSMInterface, DistanceInterface
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

        $max = 0;
        $aux = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $aux += abs($a[$key] - $b[$key]);
                if ($max < $aux) {
                    $max = $aux;
                }
            }
        }


        return $max;

    }


}
