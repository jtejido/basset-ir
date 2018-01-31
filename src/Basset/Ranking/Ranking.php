<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Ranking\ScoringInterface;
use Basset\Documents\DocumentInterface;
use Basset\FeatureExtraction\TfFeatureExtraction;
use Basset\Statistics\Statistics;


/**
 * A Wrapper for weighted retrieval using a specific IR scheme
 *
 * The class receives an implementation of ScoringInterface, and CollectionSet, then tokenized queries to 
 * search and compute each CollectionSet document's score.
 */

class Ranking extends AbstractRanking
{


    protected $query;

    protected $score;

    protected $type;

    protected $stats;

    public function __construct(ScoringInterface $type, CollectionSet $set)
    {
        parent::__construct($set);
        $this->stats = new Statistics($this->set);
        $this->type = $type;

        if ($this->type == null) {
            throw new \Exception("Ranking Model cannot be null.");
        }
    }



    /**
     * Returns result ordered by rank.
     *
     * @param  DocumentInterface $q
     * @return array
     */

    public function search(DocumentInterface $q)
    {

        $score = array();
        
        $terms = array_unique($q->getDocument());

        //∑(Document, Query)

        $collectionLength = $this->stats->numberofCollectionTokens();
        $collectionCount = $this->stats->numberofDocuments(); 

        foreach($this->set as $class => $doc) {
            $this->vector = $this->getVector($doc);
            $score[$class] = isset($score[$class]) ? $score[$class] : 0;
            $docLength = $this->getDocLength($doc);
            $uniqueTermsCount = $this->getUniqueTermsCount($doc); 
            foreach ($terms as $term){
                $tf = $this->getTf($doc, $term);
                $documentFrequency = $this->stats->documentFrequency($term);
                $keyFrequency = $this->keyFrequency($q->getDocument(), $term);
                $termFrequency = $this->stats->termFrequency($term);

                if($tf != 0) {
                    $score[$class] += $this->type->score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount);
                }
            }
        }

        arsort($score);
        return $score;
    }

    private function getUniqueTermsCount(DocumentInterface $d) {
        return count(array_filter(
                    $this->vector, 
                    function($term) {
                        return $term == 1;
                    }));
    }

    private function getTf(DocumentInterface $d, $term) {
        return isset($this->vector[$term]) ? $this->vector[$term] : 0; 
    }

    private function getDocLength(DocumentInterface $d) {
        return array_sum($this->vector);
    }

    private function getVector(DocumentInterface $d) {
         $tffe = new TfFeatureExtraction();
         return $tffe->getFeature($d);
    }


}