<?php

namespace Basset\Ranking\Normalization;

use Basset\Math\Math;
use Basset\Statistics\CollectionStatistics;

abstract class Normalization
{

    protected $math;

    protected $cs;


    public function __construct()
    {
        $this->math = new Math();
    }

    public function setCollectionStatistics(CollectionStatistics $cs)
    {

        $this->cs = $cs;

    }


    protected function getAverageDocumentLength()
    {

        return $this->cs->getAverageDocumentLength();
        
    }

    abstract protected function normalise($tf, $docLength);

}