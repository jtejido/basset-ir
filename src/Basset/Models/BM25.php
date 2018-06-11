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
 * BM25 is a class for ranking documents against a query.
 *
 * The implementation is based on the paper by Stephen E. Robertson, Steve Walker, Susan Jones, 
 * Micheline Hancock-Beaulieu & Mike Gatford (November 1994).
 * @see http://trec.nist.gov/pubs/trec3/t3_proceedings.html.
 *
 * Some modifications have been made to allow for non-negative scoring as suggested here.
 * @see https://doc.rero.ch/record/16754/files/Dolamic_Ljiljana_-_When_Stopword_Lists_Make_the_Difference_20091218.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BM25 extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
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
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $idf = log(1 + (($this->getNumberOfDocuments()-$this->getDocumentFrequency()+0.5)/($this->getDocumentFrequency() + 0.5)));
        $num = $tf * ($this->k1 + 1);
        $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));
        
        return $idf * ($num / $denom);

    }

}