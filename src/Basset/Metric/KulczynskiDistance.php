<?php


namespace Basset\Metric;


/**
 * @see http://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class KulczynskiDistance extends Metric implements VSMInterface, DistanceInterface
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

        if(empty($a) || empty($b)){
            throw new \InvalidArgumentException('Vector $' . (empty($a) ? 'a' : 'b') . ' is not an array');
        }
        
        $num = 0;
        $denom = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $num += abs($b[$key] - $a[$key]);
                $denom += min($b[$key], $a[$key]);
            }
        }


        return ($denom > 0) ? $num/$denom : 0;
    }

}