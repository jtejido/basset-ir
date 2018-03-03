<?php

namespace Basset\Ranking;

use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;
use Basset\Math\Math;

abstract class WeightedModel
{


    protected $cs;

    protected $es;

    protected $math;

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
   

    protected function getAverageDocumentLength()
    {

        return $this->cs->getAverageDocumentLength();
        
    }

    protected function getNumberOfTokens()
    {

        return $this->cs->getNumberOfTokens();
        
    }

    protected function getNumberOfDocuments()
    {

        return $this->cs->getNumberOfDocuments();
        
    }

    protected function getNumberOfUniqueTerms()
    {

        return $this->cs->getNumberOfUniqueTerms();
        
    }

    protected function getTermFrequency()
    {

        return $this->es->getTermFrequency();
        
    }

    protected function getDocumentFrequency()
    {

        return $this->es->getDocumentFrequency();
        
    }

    protected function getTotalByTermPresence()
    {

        return $this->es->getTotalByTermPresence();
        
    }

    protected function getDocsByTermPresence()
    {

        return $this->es->getDocsByTermPresence();
        
    }


}