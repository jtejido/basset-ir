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
 * BM25L is a work of Lv and Zhai to rewrite BM25 due to Singhal et al's observation for having it penalized
 * longer documents.
 *
 * When Documents Are Very Long, BM25 Fails! (Lv and Zhai).
 * @see http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.600.16&rep=rep1&type=pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BM25L extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const B = 0.75;

    const K1 = 1.2;

    const D = 0.5;

    protected $b;

    protected $k1;

    protected $d;

    public function __construct($b = self::B, $k1 = self::K1, $d = self::D)
    {
        parent::__construct();
        $this->b = $b;
        $this->k1 = $k1;
        $this->d = $d;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $idf = log($this->getNumberOfDocuments() + 1/$this->getDocumentFrequency() + 0.5);
        $c = $tf / (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));
        $num = ($this->k1 + 1) * ($c + $this->d);
        $denom = $this->k1 + ($c + $this->d);
        
        return $idf * ($num / $denom);

    }

}