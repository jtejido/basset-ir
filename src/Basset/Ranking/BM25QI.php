<?php

namespace Basset\Ranking;

use Basset\Ranking\ScoringInterface;

/**
 * BM25QI builds on Fang Et al.'s work by capturing interaction between IDF and query length (QLN-IDF).
 *
 * The implementation is based on the paper by Ariannezhad Et al., 
 * https://ciir-publications.cs.umass.edu/getpdf.php?id=1276
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BM25QI implements ScoringInterface
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
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount, $keylength)
    {
        $score = 0;

        if($tf != 0){
            $idf = log(($collectionCount + 1)/$documentFrequency);
            $avg_dl = $docLength/$collectionLength;
            $num = $tf * ($this->k1 + 1);
            $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $avg_dl));
            $score += (($this->k3 + 1) * $keyFrequency/($this->k3 + $keyFrequency)) * ($num / $denom) * pow(($idf + 1), log($keylength + 1));
        }

        return $score;

    }

}