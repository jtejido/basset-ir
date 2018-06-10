<?php

declare(strict_types=1);

namespace Basset\Index;

use Basset\Statistics\{
        EntryStatistics, 
        CollectionStatistics
    };



/**
 * An Inverted Index object used throughout Basset. The search is O(1) since we only use simple array.
 * Though I'm always temted to simply use this fact when searching, I've still gone for a trie structure, as it gives
 * me power to search for term as a prefix for more terms. This may come in handy when I develop a more robust way of
 * ranking terms based on co-occurence as prefix. e.g. stripping 'relevan' from 'relevant' to give score to 
 * child words like 'relevancy' or 'relevance'.
 */
class Index implements IndexInterface, \Iterator, \ArrayAccess, \Countable {


    private $entries;

    private $currentEntry;

    private $collectionStats;

    public function __construct()
    {
        $this->entries = array();
    }

    public function getData(): array
    {
        return $this->entries;
    }

    public function addEntry(string $key, EntryStatistics $value)
    {
        $this->entries[$key] = new IndexEntry($value);
    }

    public function getDocuments(): array
    {
    	$arrayclass = array();
    	foreach($this->entries as $term => $sub) {
    		$array = $sub->getValue()->getPostingList();
        	foreach($array as $class => $value) {
        			$document[$class][$term] = $value->getTf();
        	}
    	}
    	return $document;
    }

    public function setCollectionStatistics(CollectionStatistics $collectionStats)
    {
    	$this->collectionStats = $collectionStats;
    }

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
        return key($this->documents);
    }

    public function offsetSet($key,$value)
    {
        throw new \Exception('Shouldn\'t add documents this way, add them through addDocument()');
    }
    public function offsetUnset($key)
    {
        throw new \Exception('Cannot unset any document');
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