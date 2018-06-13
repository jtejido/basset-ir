<?php


namespace Basset\Models;

use Basset\Models\Contracts\{
        TFInterface,
        WeightedModelInterface
    };


/**
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TfConcaveK extends WeightedModel implements WeightedModelInterface, TFInterface
{
    const K1 = 1.2;

    protected $k1;

    public function __construct($k1 = self::K1)
    {
        parent::__construct();
        $this->k1 = $k1;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
        $num = $tf * ($this->k1 + 1);
        $denom = $tf + $this->k1;

        return $num/$denom;
    }


}