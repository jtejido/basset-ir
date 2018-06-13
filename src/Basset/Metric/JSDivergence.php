<?php


namespace Basset\Metric;

use Basset\Metric\KLDivergence;

/**
 * @see https://en.wikipedia.org/wiki/Jensen%E2%80%93Shannon_divergence
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class JSDivergence extends Metric implements VSMInterface, DistanceInterface
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
        
      $sim = new KLDivergence;
      $uniqueKeys = $this->getAllUniqueKeys($a, $b);
      foreach ($uniqueKeys as $key) {
        $average[$key] = 0;
        if (!empty($a[$key]) && !empty($b[$key])){
            $average[$key] += ($a[$key] + $b[$key])/2;
        }
      }
      return ($sim->dist($a, $average) + $sim->dist($b, $average))/2;

    }


}
