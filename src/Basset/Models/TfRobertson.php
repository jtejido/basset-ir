<?php


namespace Basset\Models;

use Basset\Models\Contracts\{
        TFInterface,
        WeightedModelInterface
    };


/**
 * RobertsonTf implementation as found in BM25
 * @see https://trec.nist.gov/pubs/trec7/papers/okapi_proc.ps.gz
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TfRobertson extends WeightedModel implements WeightedModelInterface, TFInterface
{

    protected $b;

    protected $k;
  
    const B = 0.75;

    const K = 1.2;

    public function __construct($k = self::K, $b = self::B)
    {
        parent::__construct();
        $this->b = $b;
        $this->k = $k;
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
        $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));
        $tf = $num / $denom;
        return $tf;

    }


}