<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\FeatureExtraction\FeatureExtractionInterface;
use Basset\FeatureExtraction\TfIdfFeatureExtraction;
use Basset\Statistics\Statistics;
use Basset\Similarity\SimilarityInterface;
use Basset\Similarity\DistanceInterface;


/**
 * Vector Space Model is a Class for calculating similarity between the document vector and the original query vector.
 *
 * By default, it uses tf-idf feature
 * https://en.wikipedia.org/wiki/Vector_space_model#Example:_tf-idf_weights
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class VectorSpaceModel extends AbstractRanking
{


    protected $stats;

    protected $sim;

    public function __construct($type, CollectionSet $set)
    {
        parent::__construct($set);
        $this->stats = new Statistics($this->set);
        if(($type instanceof SimilarityInterface) || ($type instanceof DistanceInterface)){
            $this->sim = $type;
        } else {
            throw new \Exception('Only instance of Similarity Interface or Distance Interface is allowed.');
        }
        $ff = new TfIdfFeatureExtraction();
        $this->tfidf = $ff->setIndex($this->stats);
    }

    /**
     * get FeatureExtraction.
     *
     * @param  FeatureExtractionInterface $ff
     * @return instance
     */
    public function setFeature(FeatureExtractionInterface $ff)
    {
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
            if($this->sim instanceof SimilarityInterface){
                $score[$class] = $this->sim->similarity($query_vector, $document_vector);
            } elseif($this->sim instanceof DistanceInterface){
                $score[$class] = $this->sim->dist($query_vector, $document_vector);
            }
        }

        arsort($score);
        return $score;

    }



}