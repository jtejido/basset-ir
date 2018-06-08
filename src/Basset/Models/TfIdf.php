<?php

namespace Basset\Models;

use Basset\Models\Contracts\WeightedModelInterface;

/**
 * TF x IDF implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TfIdf extends WeightedModel implements WeightedModelInterface
{

    
    public function __construct($base = parent::E)
    {
        $this->base = $base;
    }


    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {   

        return $this->getDocumentFrequency() > 0 ? $tf * log(1 + ($this->getNumberOfDocuments() / $this->getDocumentFrequency()), $this->base) : 0;

    }


}