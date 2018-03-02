<?php

namespace Basset\Statistics;

use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\PostingStatistics;

/**
 * EntryStatistics implements basic statistics about a lexical entry (usually a term).
 * getTermFrequency is the number of occurences of the word in the entire collection.
 * getDocumentFrequency is the number of documents containing the word in the entire collection.
 */

class EntryStatistics
{

    protected $stats;

    protected $set;

    protected $term;

    /**
     *
     * TO-DO(when persistence is done, this should be termID)
     * @param CollectionStatistics $stats The precomputed collection we want to extend
     * @param string $term The term we'll be getting stats for
     *
     */
    public function __construct(CollectionStatistics $stats, string $term)
    {

        $this->stats = $stats;
        $this->term = $term;
        $this->set = $this->stats->getCollection();
        if($this->term === null){
            throw new \Exception('Term should be set.');
        }
    }

    /**
     * Returns the term.
     * 
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Returns number of occurences of the word in the entire collection.
     * 
     * @return int
     */
    public function getTermFrequency()
    {
        $termfrequencies = $this->stats->getTermFrequencies();

        if (isset($termfrequencies[$this->term])) {
            return $termfrequencies[$this->term];
        } else {
            return 0;
        }

    }

    /**
     * Returns number of documents containing the word in the entire collection.
     * 
     * @return int
     */
    public function getDocumentFrequency()
    {

        $documentfrequencies = $this->stats->getDocumentFrequencies();

        if (isset($documentfrequencies[$this->term])) {
            return $documentfrequencies[$this->term];
        } else {
            return 0;
        }

    }

    /**
     *
     * THE FOLLOWING ARE USED FOR SPUD (JelinekMercerSPUD and DirichletSPUD) BACKGROUND ESTIMATION OF DCM, 
     * THIS IS RESOURCE EXPENSIVE, SO USE THOSE MODELS AT YOUR OWN RISK.
     *
     */

    /**
     * Returns the total number of unique terms in the only set of documents where the word appears.
     * 
     * @return int
     */
    public function getTotalByTermPresence() {
        $sum = 0;
        for($i = 0; $i < $this->stats->getNumberOfDocuments(); $i++) {
            $posting_stats = new PostingStatistics($this->set->offsetGet($i));
            if($posting_stats->getTf($this->term) > 0) {
                $sum += $posting_stats->getNumberOfUniqueTerms();
            }
        }
        return $sum;
    }

    /**
     * Returns the array of tokenized documents where the word appears (this only uses offset as key).
     * 
     * @return array
     */
    public function getDocsByTermPresence() {
        for($i = 0; $i < $this->stats->getNumberOfDocuments(); $i++) {
            $posting_stats = new PostingStatistics($this->set->offsetGet($i));
            if($posting_stats->getTf($this->term) > 0) {
                $array[$i] = $this->set->offsetGet($i)->getDocument();
            }
        }
        return $array;
    }

}