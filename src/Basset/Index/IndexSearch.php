<?php

namespace Basset\Index;

use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\EntryStatistics;
use Basset\Structure\TrieManager;


/**
 * The IndexSearch takes in what is read and built by IndexReader for any searching operations in the index.
 * It can be used as stand-alone class to search for items added in the index provided you've pre-processed it in 
 * the IndexReader.
 */
    
class IndexSearch
{

    private $indexReader;

    private $trieManager;

    private $indexManager;

    public function __construct(IndexReader $indexReader)
    {
        $this->indexReader = $indexReader;

        if($this->indexReader === null){
            throw new \Exception('IndexReader is null.');
        }

        $this->index = $indexReader;

        $this->indexManager = $this->indexReader->getIndexManager();

        $this->trieManager = $this->indexReader->getTrieManager();
    }

    public function search(string $term)
    {
        $stats = $this->trieManager->search($term);

        foreach ($stats as $key => $stat) {
              if($key == $term) {
                 return $stat->getValue();
              } else {
                 return false;
              }
         }
    }

    public function searchPrefix(string $term)
    {
        $stats = $this->trieManager->search($term);

        return $stats;
    }

    public function getDocuments()
    {
        return $this->indexManager->getDocuments();
    }

    public function getCollectionStatistics(): CollectionStatistics
    {
        return $this->indexManager->getCollectionStatistics();
    }


}
