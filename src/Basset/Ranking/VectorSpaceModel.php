<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\FeatureExtraction\FeatureExtractionInterface;
use Basset\FeatureExtraction\TfIdfFeatureExtraction;
use Basset\Statistics\Statistics;
use Basset\Similarity\SimilarityInterface;


/**
 * Vector Space Model is a Class for calculating Relevance ranking by comparing the deviation of angles
 * between each document vector and the original query vector.
 *
 * It uses Cosine Similarity as similarity measure between tfidf vector matrices.
 * You can use current implementation of cosine similarity but it was made to return an
 * Exception in case of 0 vector product instead of 0.
 *
 * https://en.wikipedia.org/wiki/Vector_space_model#Example:_tf-idf_weights
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class VectorSpaceModel extends AbstractRanking
{


    protected $stats;

    protected $sim;

    public function __construct(SimilarityInterface $type, CollectionSet $set)
    {
        parent::__construct($set);
        $this->stats = new Statistics($this->set);
        $this->sim = $type;
        $this->tfidf = null;
    }

    /**
     * get FeatureExtraction.
     *
     * @param  FeatureExtractionInterface $ff
     * @return instance
     */
    public function setFeature(FeatureExtractionInterface $ff = null)
    {
        if ($this->tfidf === null) {
            $ff = new TfIdfFeatureExtraction();
        }

        $this->tfidf = $ff->setIndex($this->stats);
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

        foreach($this->set as $class => $doc) {
            $query_vector = $this->tfidf->getFeature($q);
            $document_vector = $this->tfidf->getFeature($doc);
            $score[$class] = $this->sim->similarity($query_vector, $document_vector);
        }

        arsort($score);
        return $score;

    }



}