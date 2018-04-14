<?php

namespace Basset\Statistics;

use Basset\Documents\DocumentInterface;
use Basset\FeatureExtraction\FeatureExtraction;


/**
 * PostingStatistics represents one posting (document) in a posting list, it uses tfFeature and extends beyond its tf
 * counting.
 * getTf Return the frequency of the term in the current document.
 * getDoclength Return the length of the document for this posting.
 */

class PostingStatistics
{

    protected $document;

    protected $preweighted;


    /**
     * @param DocumentInterface $d The posting listing (AKA document)
     */
    public function __construct(DocumentInterface $d, bool $preweighted = false)
    {
        $tffe = new FeatureExtraction($preweighted);
        $this->document = $tffe->getFeature($d);
        $this->preweighted = $preweighted;
    }

    /**
     * Return the pre-counted tokens.
     * 
     * @return Document
     */
    public function getTokens()
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
        if($this->preweighted) {
            throw new \Exception('The Posting Statistics for this document is pre-weighted.');
        }
        return isset($this->getTokens()[$term]) ? $this->getTokens()[$term] : 0; 
    }

    /**
     * Return the length of the document for this posting.
     * 
     * @return int
     */
    public function getDocumentLength()
    {
        if($this->preweighted) {
            throw new \Exception('The Posting Statistics for this document is pre-weighted.');
        }
        return array_sum($this->getTokens());
    }

    /**
     * Returns the total number of unique terms for the posting.
     * 
     * @return int
     */
    public function getNumberOfUniqueTerms()
    {
        if($this->preweighted) {
            throw new \Exception('The Posting Statistics for this document is pre-weighted.');
        }
        return count($this->getTokens());
    }

}