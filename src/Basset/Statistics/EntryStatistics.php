<?php

namespace Basset\Statistics;

use Basset\Statistics\CollectionStatistics;
use Basset\FeatureExtraction\TfFeatureExtraction;
use Basset\Ranking\Tf;

/**
 * EntryStatistics implements basic statistics about a lexical entry (usually a term).
 * getTermFrequency is the number of occurences of the word in the entire collection.
 * getDocumentFrequency is the number of documents containing the word in the entire collection.
 */

class EntryStatistics
{

    private $stats;

    private $set;

    private $term;


    /**
     *
     * TO-DO(when persistence is done, this should be termID)
     * @param CollectionStatistics $stats The precomputed collection we want to extend
     * @param string $term The term we'll be getting stats for
     *
     */
    public function __construct(CollectionStatistics $stats)
    {

        $this->stats = $stats;
        $this->set = $this->stats->getCollection();
        $this->term = null;
    }

    /**
     * sets the term we'll compute stats for.
     * 
     * @return string
     */
    public function setTerm($term = null)
    {
        if($term === null){
            throw new \Exception('Term should be set.');
        }
        $this->term = $term;
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
        if($this->getTerm() === null){
            throw new \Exception('Specify a term.');
        }

        return isset($this->stats->getTermFrequencies()[$this->getTerm()]) ? $this->stats->getTermFrequencies()[$this->getTerm()] : 0;

    }

    /**
     * Returns number of documents containing the word in the entire collection.
     * 
     * @return int
     */
    public function getDocumentFrequency()
    {
        if($this->getTerm() === null){
            throw new \Exception('Specify a term.');
        }

        return isset($this->stats->getDocumentFrequencies()[$this->getTerm()]) ? $this->stats->getDocumentFrequencies()[$this->getTerm()] : 0;


    }

    /**
     *
     * THE FOLLOWING ARE USED FOR SPUD (JelinekMercerSPUD and DirichletSPUD) BACKGROUND ESTIMATION OF DCM, 
     * THIS IS RESOURCE EXPENSIVE, SO USE THOSE MODELS AT YOUR OWN RISK.
     *
     */

    /**
     * Returns the total number of tokens in the only set of documents where the word appears.
     * 
     * @return int
     */
    public function getTotalByTermPresence() {
        
        if($this->getTerm() === null){
            throw new \Exception('Specify a term.');
        }

        $sum = 0;

        $term = $this->getTerm();

        $numberOfDocs = $this->stats->getNumberOfDocuments();

        for($i = 0; $i < $numberOfDocs; $i++) {

            $array = array_count_values($this->set->offsetGet($i)->getDocument());

            if(isset($array[$term])) {

                $sum += array_sum($array);

            }
        }
        return $sum;
    }

    /**
     * Returns the total number of unique terms in the only set of documents where the word appears.
     * 
     * @return int
     */
    public function getUniqueTotalByTermPresence() {
        
        if($this->getTerm() === null){
            throw new \Exception('Specify a term.');
        }

        $sum = 0;

        $term = $this->getTerm();

        $numberOfDocs = $this->stats->getNumberOfDocuments();

        for($i = 0; $i < $numberOfDocs; $i++) {

            $array = array_count_values($this->set->offsetGet($i)->getDocument());

            if(isset($array[$term])) {

                $sum += count($array);

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

        if($this->getTerm() === null){
            throw new \Exception('Specify a term.');
        }

        $related_docs = array();

        for($i = 0; $i < $this->stats->getNumberOfDocuments(); $i++) {

            $array = array_count_values($this->set->offsetGet($i)->getDocument());

            if(isset($array[$this->getTerm()])) {

                $related_docs[$i] = $array;

            }
        }
        return $related_docs;
    }

}