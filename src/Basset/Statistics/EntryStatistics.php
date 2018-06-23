<?php


namespace Basset\Statistics;

use Basset\Statistics\PostingStatistics;

/**
 * EntryStatistics implements basic statistics about a lexical entry (usually a term).
 * getTermFrequency is the number of occurences of the word in the entire collection.
 * getDocumentFrequency is the number of documents containing the word in the entire collection.
 */

class EntryStatistics
{

    private $termFrequency;

    private $documentFrequency;

    private $totalTermByPresence;

    private $uniqueTotalByTermPresence;

    private $postinglist;

    public function __construct()
    {
        $this->termFrequency = null;
        $this->documentFrequency = null;
        $this->totalTermByPresence = null;
        $this->uniqueTotalByTermPresence = null;
        $this->postinglist = array();
    }


    /**
     * Returns number of occurences of the word in the entire collection.
     * 
     * @return int
     */
    public function getTermFrequency(): int
    {
        return $this->termFrequency;
    }

    /**
     * Returns number of documents containing the word in the entire collection.
     * 
     * @return int
     */
    public function getDocumentFrequency(): int
    {
        return $this->documentFrequency;
    }

    /**
     * Returns the total number of tokens in the only set of documents where the word appears.
     * 
     * @return int
     */
    public function getTotalByTermPresence(): int
    {
        return $this->totalTermByPresence;
    }

    /**
     * Returns the total number of unique terms in the only set of documents where the word appears.
     * 
     * @return int
     */
    public function getUniqueTotalByTermPresence(): int
    {
        return $this->uniqueTotalByTermPresence;
    }

    /**
     * Returns the posting list for the term.
     * 
     * @return int
     */
    public function getPostingList(): array
    {
        return $this->postinglist;
    }

    public function setTermFrequency(int $value)
    {
        $this->termFrequency = $value;
    }

    public function setDocumentFrequency(int $value)
    {
        $this->documentFrequency = $value;
    }

    public function setTotalByTermPresence(int $value)
    {
        $this->totalTermByPresence = $value;
    }

    public function setUniqueTotalByTermPresence(int $value)
    {
        $this->uniqueTotalByTermPresence = $value;
    }

    public function setPostingList(int $id, PostingStatistics $value)
    {
        $this->postinglist[$id] = $value;
    }



}