<?php

namespace Basset\Ranking\AfterEffect;

use Basset\Statistics\EntryStatistics;

abstract class AfterEffect
{
	protected $es;

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
    
    abstract protected function gain($tf);

}