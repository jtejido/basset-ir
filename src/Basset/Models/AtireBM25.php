<?php

namespace Basset\Models;

use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Metric\VectorSimilarity;
use Basset\Models\TermCount;

/**
 * ATIRE BM25 is a class that uses Robertson-Walker IDF instead of the original Robertson-Sparck IDF.
 *
 * Towards an Efficient and Effective Search Engine (Trotman, Jia, Crane).
 * SIGIR 2012 Workshop on Open Source Information Retrieval.
 * http://opensearchlab.otago.ac.nz/paper_4.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class AtireBM25 extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const B = 0.75;

    const K1 = 1.2;

    protected $b;

    protected $k1;

    protected $d;

    public function __construct($b = self::B, $k1 = self::K1)
    {
        parent::__construct();
        $this->b = $b;
        $this->k1 = $k1;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        $idf = log($this->getNumberOfDocuments()/$this->getDocumentFrequency());
        $num = $tf * ($this->k1 + 1);
        $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));
        
        return $idf * ($num / $denom);

    }

}