<?php

namespace Basset\Statistics;

use Basset\Collections\CollectionSet;
use Basset\FeatureExtraction\FeatureExtractionInterface;
use Basset\FeatureExtraction\DataAsFeatures;


/**
 * Statistics implements global collection statistics for use in different ranking schemes (DFR,VSM, etc.).
 * numberofTokens is the number of all tokens in the entire collection.
 * numberofDocuments is the number of documents in the collection.
 * termFrequency is the number of occurences of the word in the entire collection.
 * documentFrequency is the number of documents containing the word in the entire collection.
 */

class CollectionStatistics
{
    protected $numberofDocuments;
    protected $termFrequency;
    protected $documentFrequency;
    protected $set;

    /**
     * @param CollectionSet $set The set of documents for which we will compute token stats
     * @param FeatureExtractionInterface $fe A feature factory to translate the document data to 
     * single tokens
     */
    public function __construct(CollectionSet $set, FeatureExtractionInterface $fe=null)
    {

        if ($fe===null){
            $fe = new DataAsFeatures();
        }

        $this->set = $set;

        $this->numberofDocuments = 0;
        foreach ($this->set as $class=>$doc) {
            $this->numberofDocuments++;
            $tokens = $fe->getFeature($doc);
            $flag = array();
            foreach ($tokens as $term) {
                $flag[$term] = isset($flag[$term]) && $flag[$term] === true ? true : false;

                if (isset($this->termFrequency[$term])){
                    $this->termFrequency[$term]++;
                } else {
                    $this->termFrequency[$term] = 1;
                }

                if (isset($this->documentFrequency[$term])){
                    if ($flag[$term] === false){
                        $flag[$term] = true;
                        $this->documentFrequency[$term]++;
                    }
                } else {
                    $flag[$term] = true;
                    $this->documentFrequency[$term] = 1;
                }
            }
            
        }

    }


    /**
     * Returns number of documents in the collection.
     * 
     * @return mixed
     */
    public function getNumberOfDocuments()
    {

        return $this->numberofDocuments;

    }

    /**
     * Returns number of occurences of all words in the entire collection.
     * 
     * @param  string $term
     * @return int
     */
    public function getTermFrequencies()
    {
        return $this->termFrequency;
    }

    /**
     * Returns average document length.
     * 
     * @return mixed
     */
    public function getAverageDocumentLength()
    {

        return array_sum($this->termFrequency)/$this->numberofDocuments;

    }

    /**
     * Returns number of documents containing all words in the entire collection.
     * 
     * @param  string $term
     * @return int
     */
    public function getDocumentFrequencies()
    {

        return $this->documentFrequency;

    }

    /**
     * Returns total number of all tokens in the entire collection.
     * 
     * @return int
     */
    public function getNumberOfTokens()
    {
        return array_sum($this->termFrequency);
    }

    /**
     * Returns the total number of unique terms in the collection.
     * 
     * @return int
     */
    public function getNumberOfUniqueTerms()
    {
        return count($this->termFrequency);
    }

    /**
     * Returns the collection set.
     * 
     * @return Collection
     */
    public function getCollection()
    {
        return $this->set;
    }

}