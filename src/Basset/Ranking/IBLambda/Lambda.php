<?php

namespace Basset\Ranking\IBLambda;

use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;

abstract class Lambda
{


    protected $cs;

    protected $es;


    public function setCollectionStatistics(CollectionStatistics $cs)
    {

        $this->cs = $cs;

    }
   

    public function setEntryStatistics(EntryStatistics $es)
    {

        $this->es = $es;

    }


    protected function getTermFrequency()
    {

        return $this->es->getTermFrequency();
        
    }

    protected function getDocumentFrequency()
    {

        return $this->es->getDocumentFrequency();
        
    }

    protected function getNumberOfDocuments()
    {

        return $this->cs->getNumberOfDocuments();
        
    }

    abstract protected function getLambda();

}