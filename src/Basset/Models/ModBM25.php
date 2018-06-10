<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermCount
    };

/**
 * ModBM25 is a modified version of BM25 that ensures negative IDF don't violate Term-Frequency, Length Normalization and 
 * TF-LENGTH Constraints by using Robertson-Sparck Idf.
 *
 * The implementation is based on the paper by Fang Et al., 
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.59.1189&rep=rep1&type=pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class ModBM25 extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const B = 0.75;

    const K1 = 1.2;

    protected $b;

    protected $k1;

    public function __construct($b = self::B, $k1 = self::K1)
    {
        parent::__construct();
        $this->b = $b;
        $this->k1 = $k1;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }

    /**
     * We'll use pivoted normalized Idf as BM25's Idf.
     * 
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

            $idf = log(($this->getNumberOfDocuments() + 1)/$this->getDocumentFrequency());
            $num = $tf * ($this->k1 + 1);
            $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));

            return ($num / $denom) * $idf;

    }

}