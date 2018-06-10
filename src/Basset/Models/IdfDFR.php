<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        IDFInterface,
        WeightedModelInterface
    };
use Basset\Metric\CosineSimilarity;

/**
 * DFR's idf implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IdfDFR extends BaseIdf implements WeightedModelInterface, IDFInterface
{

    public function __construct()
    {
        // DFR's idf is always binary log
        parent::__construct(2);
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
        return $df > 0 ? log(1 + (($this->getNumberOfDocuments()+1)/($df+0.5)), $this->getBase()) : 0;

    }


}