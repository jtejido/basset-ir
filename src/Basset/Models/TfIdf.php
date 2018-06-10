<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Metric\CosineSimilarity;

/**
 * TF x IDF implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TfIdf extends WeightedModel implements WeightedModelInterface
{

    
    public function __construct($base = parent::E)
    {
        parent::__construct();
        $this->base = $base;
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
        return $df > 0 ? $tf * log(1 + ($this->getNumberOfDocuments() / $df), $this->base) : 0;

    }


}