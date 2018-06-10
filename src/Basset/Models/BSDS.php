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
 * BSDS is a class that implements the Binary Standard Document Score (BSDS) with document length normalization. 
 *
 * The implementation is based on Ronan Cummins' paper:
 * A Standard Document Score for Information Retrieval.
 * http://dcs.gla.ac.uk/~ronanc/papers/cumminsICTIR13.pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BSDS extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const K1 = 1.2;

    const B = 0.4;

    protected $k1;

    protected $b;

    public function __construct($b = self::B, $k1 = self::K1)
    {
        parent::__construct();
        $this->k1 = $k1;
        $this->b = $b;
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

        $tf_num = $tf * ($this->k1 + 1);
        $tf_denom = $tf + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength()));;
        $tfr = $tf_num / $tf_denom;
        $num = $tfr - ($this->getDocumentFrequency() / $this->getNumberOfDocuments());
        $denom = sqrt(($this->getDocumentFrequency() - (pow($this->getDocumentFrequency(), 2) / $this->getNumberOfDocuments())) / $this->getNumberOfDocuments());
        return $denom > 0 ? $num / $denom : 0;

    }


}