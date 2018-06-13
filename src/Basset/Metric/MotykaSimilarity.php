<?php


namespace Basset\Metric;


/**
 * @see http://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf
 * Note: Be aware that this gives 0.5 to show that 2 arrays are equal
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class MotykaSimilarity extends Metric implements VSMInterface, SimilarityInterface
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
    public function similarity(array $a, array $b): float
    {

        $num = 0;
        $denom = 0;
        $uniqueKeys = $this->getAllUniqueKeys($a, $b);

        foreach ($uniqueKeys as $key) {
            if (!empty($a[$key]) && !empty($b[$key])){
                $num += min($b[$key], $a[$key]);
                $denom += ($b[$key] + $a[$key]);
            }
        }


        return ($denom > 0) ? ($num/$denom) : 0;
    }

}