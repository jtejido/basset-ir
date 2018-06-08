<?php

namespace Basset\Models;

use Basset\Models\Contracts\IDFInterface;
use Basset\Models\Contracts\WeightedModelInterface;

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
    }
    
    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {
        
        return $this->getDocumentFrequency() > 0 ? log(1 + (($this->getNumberOfDocuments()-$this->getDocumentFrequency()+0.5)/($this->getDocumentFrequency() + 0.5)), $this->getBase()) : 0;

    }


}