<?php


namespace Basset\Index;

use Basset\Statistics\{
        EntryStatistics, 
        CollectionStatistics
    };
use Basset\MetaData\MetaData;

/**
 * An Inverted Index object used throughout Basset. This holds statistical information as IndexEntry from a given term (key).
 * The search is O(1) since we only use simple array object.
 * This also holds a CollectionStatistics property that provides an overall counted statistics and postings for metadata 
 * for a given docId.
 * 
 * @see EntryStatistics
 * @see CollectionStatistics
 * @see IndexEntry
 *
 * @var $entries
 * @var $collectionStats
 * @var $currentEntry
 *
 * @example 
 * $index = new Index;
 * $index->setCollectionStatistics($collectionStats);
 * $index->addEntry('word', $entryStatistics);
 * $index->getCollectionStatistics();
 * $index->getData();
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Index implements IndexInterface {


    private $entries;

    private $postings;

    private $currentEntry;

    private $collectionStats;

    /**
     * Initializes $entries and $postings.
     */
    public function __construct()
    {
        $this->entries = array();
        $this->postings = array();
        $this->collectionStats = null;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->entries;
    }

    /**
     * @return array
     */
    public function getAllMetadata(): array
    {
        return $this->postings;
    }

    /**
     * @param int $id The Document ID.
     * @param MetaData $value The corresponding MetaData for document.
     */
    public function addMetaData(int $id, MetaData $value)
    {
        $this->postings[$id] = $value;
    }

    /**
     * @return MetaData
     */
    public function getMetaData(int $id): MetaData
    {
        return $this->postings[$id];
    }

    /**
     * @param string $key The term to be added as key.
     * @param EntryStatistics $value The EntryStatistics for the given term wrapped in IndexEntry.
     */
    public function addEntry(string $key, EntryStatistics $value)
    {
        $this->entries[$key] = new IndexEntry($value);
    }

    /**
     * @param CollectionStatistics $collectionStats The collection statistics.
     */
    public function setCollectionStatistics(CollectionStatistics $collectionStats)
    {
    	$this->collectionStats = $collectionStats;
    }

    /**
     * @return CollectionStatistics.
     */
    public function getCollectionStatistics(): CollectionStatistics 
    {
    	return $this->collectionStats;
    }

    public function count() 
    {
        return count($this->entries);
    }

     public function rewind()
    {
        reset($this->entries);
        $this->currentEntry = current($this->entries);
    }

    public function next()
    {
        $this->currentEntry = next($this->entries);
    }

    public function valid()
    {
        return $this->currentEntry != false;
    }

    public function current()
    {
        return $this->currentEntry;
    }

    public function key()
    {
        return key($this->entries);
    }

    public function offsetSet($key,$value)
    {
        throw new \Exception('Shouldn\'t add term this way, add them through addEntry()');
    }
    public function offsetUnset($key)
    {
        throw new \Exception('Cannot unset any entry');
    }
    public function offsetGet($key)
    {
        return $this->entries[$key];
    }
    public function offsetExists($key)
    {
        return isset($this->entries[$key]);
    }


}