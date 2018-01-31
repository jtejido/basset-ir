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
 * hapaxes returns an array of unique terms from a document with a known $key.
 */

class Statistics
{
    protected $numberofCollectionTokens;
    protected $numberofDocuments;
    protected $termFrequency;
    protected $documentFrequency;


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

        $this->numberofCollectionTokens = 0;
        $this->numberofDocuments = 0;
        foreach ($set as $class=>$doc) {
            $this->numberofDocuments++;
            $tokens = $fe->getFeature($doc);
            $flag = array();
            foreach ($tokens as $term) {
                $this->numberofCollectionTokens++;
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
     * Returns the idf weight containing the query word in the entire collection.
     * 
     * @param  string $term
     * @return mixed
     */
    public function idf($term)
    {

        if (isset($this->documentFrequency[$term])) {
            return log($this->numberofDocuments/$this->documentFrequency[$term]);
        } else {
            return log($this->numberofDocuments);
        }

    }

    /**
     * Returns the smoothed idf weight containing the query word in the entire collection.
     * 
     * @param  string $term
     * @return mixed
     */
    public function smoothedidf($term)
    {

        if (isset($this->documentFrequency[$term])) {
            return log(1+($this->numberofDocuments/$this->documentFrequency[$term]));
        } else {
            return log($this->numberofDocuments);
        }

    }

    /**
     * Returns number of documents in the collection.
     * 
     * @return mixed
     */
    public function numberofDocuments()
    {

        return $this->numberofDocuments;

    }

    /**
     * Returns number of occurences of the word in the entire collection.
     * 
     * @param  string $term
     * @return int
     */
    public function termFrequency($term = null)
    {
        if($term != null) {
            if (isset($this->termFrequency[$term])) {
                return $this->termFrequency[$term];
            } else {
                return 0;
            }
        } else {
            return $this->termFrequency;
        }
    }

    /**
     * Returns number of documents containing the word in the entire collection.
     * 
     * @param  string $term
     * @return int
     */
    public function documentFrequency($term = null)
    {
        if($term != null) {
            if (isset($this->documentFrequency[$term])) {
                return $this->documentFrequency[$term];
            } else {
                return 0;
            }
        } else {
            return $this->documentFrequency;
        }
    }

    /**
     * Returns total number of all tokens in the entire collection.
     * 
     * @return int
     */
    public function numberofCollectionTokens()
    {
        return $this->numberofCollectionTokens;
    }


}