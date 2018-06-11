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
 * BM25 is a class for ranking documents against a query where we made use of a delta(δ) value of 1, 
 * which modifies BM25 to account for an issue against penalizing long documents and allowing shorter ones to dominate. 
 * The delta values assures BM25 to be lower-bounded.
 * @see http://sifaka.cs.uiuc.edu/~ylv2/pub/cikm11-lowerbound.pdf
 *
 * Some modifications have been made to allow for non-negative scoring as suggested here.
 * @see https://doc.rero.ch/record/16754/files/Dolamic_Ljiljana_-_When_Stopword_Lists_Make_the_Difference_20091218.pdf
 *
 * We made use of a delta(δ) value of 1, which modifies BM25 to account for an issue against
 * penalizing long documents and allowing shorter ones to dominate. The delta values assures BM25
 * to be lower-bounded. (This makes this class BM25+)
 * @see http://sifaka.cs.uiuc.edu/~ylv2/pub/cikm11-lowerbound.pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BM25Plus extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const B = 0.75;

    const K1 = 1.2;

    const D = 1;

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

        $idf = log(1 + (($this->getNumberOfDocuments()-$this->getDocumentFrequency()+0.5)/($this->getDocumentFrequency() + 0.5)));
        $num = $tf * ($this->k1 + 1);
        $denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));

        return $idf * (($num / $denom) + $this->d);

    }

}