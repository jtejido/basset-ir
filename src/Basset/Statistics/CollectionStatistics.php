<?php

declare(strict_types=1);

namespace Basset\Statistics;



/**
 * Statistics implements global collection statistics for use in different ranking schemes (DFR,VSM, etc.).
 * numberofTokens is the number of all tokens in the entire collection.
 * numberofDocuments is the number of documents in the collection.
 * termFrequency is the number of occurences of the word in the entire collection.
 * documentFrequency is the number of documents containing the word in the entire collection.
 */

class CollectionStatistics
{
    private $numberOfDocuments;

    private $averageDocumentLength;

    private $numberOfTokens;

    private $numberOfUniqueTokens;


    public function __construct()
    {
        $this->numberOfDocuments = null;
        $this->averageDocumentLength = null;
        $this->numberOfTokens = null;
        $this->numberOfUniqueTokens = null;
    }


    /**
     * Returns number of documents in the collection.
     * 
     * @return mixed
     */
    public function getNumberOfDocuments(): int
    {

        return $this->numberofDocuments;

    }

    /**
     * Returns average document length.
     * 
     * @return mixed
     */
    public function getAverageDocumentLength(): float
    {

        return $this->averageDocumentLength;

    }

    /**
     * Returns total number of all tokens in the entire collection.
     * 
     * @return int
     */
    public function getNumberOfTokens(): int
    {
        return $this->numberOfTokens;
    }

    /**
     * Returns the total number of unique terms in the collection.
     * 
     * @return int
     */
    public function getNumberOfUniqueTokens(): int
    {
        return $this->numberOfUniqueTokens;
    }

    /**
     * Returns number of documents in the collection.
     * @param  int $value
     */
    public function setNumberOfDocuments(int $value)
    {

        $this->numberofDocuments = $value;

    }

    /**
     * Returns average document length.
     * @param  float $value
     */
    public function setAverageDocumentLength(float $value)
    {

        $this->averageDocumentLength = $value;

    }

    /**
     * Returns total number of all tokens in the entire collection.
     * @param  int $value
     */
    public function setNumberOfTokens(int $value)
    {
        $this->numberOfTokens = $value;
    }

    /**
     * Returns the total number of unique terms in the collection.
     * @param  int $class
     */
    public function setNumberOfUniqueTokens(int $value)
    {
        $this->numberOfUniqueTokens = $value;
    }


}