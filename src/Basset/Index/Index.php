<?php

namespace Basset\Index;

use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\EntryStatistics;
use Basset\Collections\CollectionSet;
use Basset\FeatureExtraction\FeatureExtractionInterface;


class Index implements IndexInterface
{

    public function __construct(CollectionSet $set, FeatureExtractionInterface $fe=null)
    {
        $this->collectionstats = new CollectionStatistics($set, $fe);
        $this->entrystats = new EntryStatistics($this->collectionstats);
    }

    public function getCollectionStatistics() {
    	return $this->collectionstats;
    }

    public function getEntryStatistics() {
    	return $this->entrystats;
    }

    public function setTerm(string $term) {
    	return $this->getEntryStatistics()->setTerm($term);
    }
}
