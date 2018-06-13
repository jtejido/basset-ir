<?php


namespace Basset\Models;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Metric\CosineSimilarity;

/**
 * PivotedConcaveTF with K. Sparck-Jones' IDF
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class PivotedConcaveTFIDF extends WeightedModel implements WeightedModelInterface
{

    const B = 0.20;

    const D = 0.5;

    protected $b;

    protected $d;

    public function __construct($b = self::B, $d = self::D)
    {
        parent::__construct();
        $this->d = $d;
        $this->b = $b;
        $this->metric = new CosineSimilarity;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $num = $tf;
        $df = $this->getDocumentFrequency();
        $denom = 1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength());
        $idf = $df > 0 ? log(($this->getNumberOfDocuments() + 1)/$df) : 0;
        
        return (1+log(1+log(($num/$denom) + $this->d))) * $idf;

    }

}