<?php

declare(strict_types=1);

namespace Basset\Metric;

use Basset\Metric\KLDivergence;

/**
 * @see https://en.wikipedia.org/wiki/R%C3%A9nyi_entropy
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class RenyiDivergence extends Metric implements VSMInterface, SimilarityInterface
{
    
    CONST CONSTANT = 2;

    protected $const;

    /**
     * https://en.wikipedia.org/wiki/R%C3%A9nyi_entropy
     */

    public function __construct($constant = self::CONSTANT)
    {
      parent::__construct();
      $this->const = $constant;
    }

    /**
     * @param  array $a
     * @param  array $b
     * @return float
     */
    public function similarity(array $a, array $b): float
    {
        $sim = new KLDivergence;
        if($this->const == 1){
            //const = 1 is a special case where it should equal KL divergence
            return $sim->dist($a, $b);
        }

        $uniqueKeys = $this->getAllUniqueKeys($a, $b);
        $divergence = 0;
        foreach ($uniqueKeys as $key) {

            if (!empty($a[$key]) && !empty($b[$key])){
                $num = pow($b[$key], $this->const);
                $denom = pow($a[$key], $this->const - 1);
                $divergence += ($denom > 0) ? ($num/$denom) : 0;
            }
        }

        return $divergence > 0 ? (1/($this->const - 1)) * log($divergence) : 0;
    }

}
