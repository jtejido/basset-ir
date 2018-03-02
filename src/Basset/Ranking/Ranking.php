<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Ranking\ScoringInterface;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\PostingStatistics;
use Basset\Statistics\EntryStatistics;


/**
 * A Wrapper for weighted retrieval using a specific IR scheme
 *
 * The class receives an implementation of ScoringInterface, and CollectionSet, then QueryInterface to 
 * search.
 *
 * The indexing happens to start from here.
 */

class Ranking extends AbstractRanking
{


    protected $type;

    protected $collectionstats;


    public function __construct(ScoringInterface $type, CollectionSet $set)
    {
        parent::__construct($set);
        $this->collectionstats = new CollectionStatistics($this->set);
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
        $term_stats = new PostingStatistics($q); //get query index
        $terms = array_keys($term_stats->getDocument());

        $this->type->setCollectionStatistics($this->collectionstats);
        foreach($this->set as $class => $doc) {
            $score[$class] = isset($score[$class]) ? $score[$class] : 0;
            $posting_stats = new PostingStatistics($doc); //get doc index
            $docLength = $posting_stats->getDocumentLength();
            $docUniqueLength = $posting_stats->getNumberOfUniqueTerms();
            foreach ($terms as $term){
                $entrystats = new EntryStatistics($this->collectionstats, $term);
                $this->type->setEntryStatistics($entrystats);
                $keylength = $term_stats->getDocumentLength();
                $tf = $posting_stats->getTf($term);
                $keyFrequency = $term_stats->getTf($term);
                if($tf > 0) {
                    $score[$class] += $this->type->score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength);
                }
            }
        }

        arsort($score);
        return $score;
    }

}