<?php

namespace Basset\Ranking;


/**
 * ModBM25 is a modified version of BM25 that ensures negative IDF don't violate Term-Frequency, Length Normalization and 
 * TF-LENGTH Constraints.
 *
 * The implementation is based on the paper by Fang Et al., 
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.59.1189&rep=rep1&type=pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class ModBM25 extends SimilarityBase implements ScoringInterface
{

    const B = 0.75;

    const K1 = 1.2;

    const K3 = 1000;

    protected $b;

    protected $k1;

    protected $k3;

    public function __construct($b = self::B, $k1 = self::K1, $k3 = self::K3)
    {
        $this->b = $b;
        $this->k1 = $k1;
        $this->k3 = $k3;
    }

    /**
     * We'll use pivoted normalized Idf as BM25's Idf.
     * 
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @param  int $keyFrequency
     * @param  int $keylength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength)
    {
        $score = 0;

        if($tf > 0){
            $idf = log(($this->getNumberOfDocuments() + 1)/$this->getDocumentFrequency());
            $num = $tf * ($this->k1 + 1);
            $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));
            $score += (($this->k3 + 1) * $keyFrequency/($this->k3 + $keyFrequency)) * ($num / $denom) * $idf;
        }

        return $score;

    }

}