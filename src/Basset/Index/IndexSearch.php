<?php


namespace Basset\Index;

use Basset\Feature\FeatureVector;
use Basset\Statistics\{
        EntryStatistics, 
        CollectionStatistics
    };
use Basset\Statistics\{
        TrieManager, 
        TrieCollection
    };



/**
 * The IndexSearch takes in what index is read and trie built by the IndexReader for any searching operations 
 * in the index.
 * It can be used as stand-alone class to search for items added in the index provided you've pre-processed it in 
 * the IndexReader.
 * 
 * @see TrieManager
 * @see TrieCollection
 * @see EntryStatistics
 * @see CollectionStatistics
 *
 * @var $indexReader
 * @var $trieManager
 * @var $indexManager
 *
 * @example 
 * $indexSearch = new IndexSearch($indexReader);
 * $indexSearch->search($term);
 * $indexSearch->searchPrefix($term);
 * $indexSearch->getDocuments();
 * $indexSearch->getCollectionStatistics();
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
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

    public function search(string $term): ?EntryStatistics
    {
        $entry = $this->indexManager->search($term);
        if($entry) {
            return $entry->getValue();
        } else {
            return null;
        }
    }

    public function searchPrefix(string $term): TrieCollection
    {
        $stats = $this->trieManager->search($term);

        return $stats;
    }

    public function getDocuments(): ?array
    {
        return $this->indexManager->getDocuments();
    }

    public function getDocumentVectors(): ?array
    {
        return $this->indexManager->getDocumentVectors();
    }

    public function getDocumentVector(string $class): FeatureVector
    {
        return $this->indexManager->getDocumentVector($class);
    }

    public function getCollectionStatistics(): CollectionStatistics
    {
        return $this->indexManager->getCollectionStatistics();
    }


}
