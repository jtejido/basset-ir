<?php

namespace Basset\Ranking;

use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\EntryStatistics;


/**
 *
 * This class should be extended by Scoring types
 *
 */

abstract class SimilarityBase
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


    abstract protected function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength);

}