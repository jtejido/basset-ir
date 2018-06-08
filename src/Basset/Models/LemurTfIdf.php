<?php

namespace Basset\Models;

use Basset\Models\Contracts\WeightedModelInterface;

/**
 * LemurTfIdf as implemented in Lemur toolkit implementation AKA Robertson's TF x IDF
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class LemurTfIdf extends WeightedModel implements WeightedModelInterface
{

    protected $b;

    protected $k;
  
    const B = 0.75;

    const K = 1.2;

    public function __construct($k = self::K, $b = self::B)
    {
        $this->b = $b;
        $this->k = $k;
    }


    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        $num = $tf * ($this->k1 + 1);
        $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));
        $tf = $num / $denom;
        $idf = log(1 + ($this->getNumberOfDocuments()/$this->getDocumentFrequency()));
        return $tf * $idf;

    }


}