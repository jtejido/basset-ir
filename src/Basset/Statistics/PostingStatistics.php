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

    private $tf;

    public function __construct()
    {
        $this->tf = null;
    }

    /**
     * Return the frequency of the term in the current document.
     * 
     * @param  string $term
     * @return int
     */
    public function getTf(): int
    {
        return $this->tf; 
    }

    /**
     * Return the frequency of the term in the current document.
     * 
     * @param  string $term
     * @return int
     */
    public function setTf(int $tf)
    {
        $this->tf = $tf; 
    }

}