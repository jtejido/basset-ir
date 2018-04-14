<?php

namespace Basset\Models;

use Basset\Models\Contracts\IDFInterface;
use Basset\Models\Contracts\WeightedModelInterface;

/**
 * idf implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class Idf extends BaseIdf implements WeightedModelInterface, IDFInterface
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
        
        return log(($this->getNumberOfDocuments()/$this->getDocumentFrequency()), $this->getBase());

    }


}