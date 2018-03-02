<?php

namespace Basset\Statistics;

use Basset\Documents\DocumentInterface;
use Basset\FeatureExtraction\TfFeatureExtraction;


/**
 * PostingStatistics represents one posting (document) in a posting list, it uses tfFeature and extends beyond its tf
 * counting.
 * getTf Return the frequency of the term in the current document.
 * getDoclength Return the length of the document for this posting.
 */

class PostingStatistics
{

    protected $document;


    /**
     * @param DocumentInterface $d The posting listing (AKA document)
     */
    public function __construct(DocumentInterface $d)
    {
        $tffe = new TfFeatureExtraction();
        $this->document = $tffe->getFeature($d);

    }

    /**
     * Return the document.
     * 
     * @return Document
     */
    public function getDocument()
    {

        return $this->document; 

    }

    /**
     * Return the frequency of the term in the current document.
     * 
     * @param  string $term
     * @return int
     */
    public function getTf($term)
    {

        return isset($this->document[$term]) ? $this->document[$term] : 0; 

    }

    /**
     * Return the length of the document for this posting.
     * 
     * @return int
     */
    public function getDocumentLength()
    {

        return array_sum($this->document);

    }

    /**
     * Returns the total number of unique terms for the posting.
     * 
     * @return int
     */
    public function getNumberOfUniqueTerms()
    {

        return count($this->document);

    }

}