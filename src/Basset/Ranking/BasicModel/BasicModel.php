<?php

namespace Basset\Ranking\BasicModel;

use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;
use Basset\Math\Math;

abstract class BasicModel
{

    protected $math;

    protected $cs;

    protected $es;


    public function __construct()
    {
        $this->math = new Math();
    }

    

    public function setCollectionStatistics(CollectionStatistics $cs)
    {

        $this->cs = $cs;

    }

    public function setEntryStatistics(EntryStatistics $es)
    {

        $this->es = $es;

    }

    protected function getNumberOfTokens()
    {

        return $this->cs->getNumberOfTokens();
        
    }

    protected function getNumberOfDocuments()
    {

        return $this->cs->getNumberOfDocuments();
        
    }

    protected function getTermFrequency()
    {

        return $this->es->getTermFrequency();
        
    }

    protected function getDocumentFrequency()
    {

        return $this->es->getDocumentFrequency();
        
    }

    abstract protected function score($tf);

    protected function idfDFR($collectionCount, $d) {
        return $this->math->DFRlog(($collectionCount+1)/($d+0.5));
    }

}