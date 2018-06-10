<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        IDFInterface,
        WeightedModelInterface
    };
use Basset\Metric\CosineSimilarity;

/**
 * Okapi BM25's idf implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IdfOkapi extends BaseIdf implements WeightedModelInterface, IDFInterface
{

    public function __construct($base = parent::E)
    {
        parent::__construct($base);
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
        $df = $this->getDocumentFrequency();
        return $df > 0 ? log(1 + (($this->getNumberOfDocuments()-$df+0.5)/($df + 0.5)), $this->getBase()) : 0;

    }


}