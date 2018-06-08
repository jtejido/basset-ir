<?php

namespace Basset\Models;

use Basset\Models\Contracts\IDFInterface;
use Basset\Models\Contracts\WeightedModelInterface;

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
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {
        
        return $this->getDocumentFrequency() > 0 ? log(1 + (($this->getNumberOfDocuments()+1)/($this->getDocumentFrequency()+0.5)), $this->getBase()) : 0;

    }


}