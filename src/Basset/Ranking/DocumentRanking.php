<?php

namespace Basset\Ranking;

use Basset\Documents\DocumentInterface;
use Basset\Index\IndexInterface;
use Basset\Statistics\EntryStatistics;
use Basset\Statistics\PostingStatistics;
use Basset\Similarity\SimilarityInterface;
use Basset\Similarity\DistanceInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\KLDivergenceLMInterface;
use Basset\Models\TermFrequency;
use Basset\Models\Contracts\TFInterface;
use Basset\Similarity\VectorSimilarity;


/**
 * DocumentRanking is a wrapper for ranking sets of documents given a query (both weighted by a specified model)
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DocumentRanking
{

    private $index;

    private $documentmodel;

    private $querymodel;

    private $sim;

    private $query;

    public function __construct(IndexInterface $index = null)
    {

        if($index === null) {
            throw new \Exception('Please set Index.');
        }

        $this->index = $index;
        $this->sim = null;
        $this->query = null;
        $this->documentmodel = null;
        $this->querymodel = null;
    }

    /**
     * get Collection Statistics.
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * get Collection Set.
     */
    public function getCollection()
    {
        return $this->getIndex()->getCollectionStatistics()->getCollection();
    }

    /**
     * Set Document's weighting model.
     *
     * @param  WeightedModelInterface $model
     */
    public function documentModel(WeightedModelInterface $model)
    {
        $this->getCollection()->applyModel($model);
    }

    /**
     * Set Query's weighting model.
     *
     * @param  QueryModelInterface $model
     */
    public function queryModel(WeightedModelInterface $model)
    {
        if($this->getQuery() === null) {
            throw new \Exception('Please set a Query.');
        }

        if($this->getDocumentModel() === null) {
            throw new \Exception('Please set a Document model.');
        }

        if($this->getDocumentModel() instanceof KLDivergenceLMInterface && !$model instanceof TermFrequency) {
            throw new \Exception('Language Models have to be used with TermFrequency weighting model');
        }

        $this->getQuery()->setModel($model);
    }

    /**
     * Set similarity. To be used for ranking documents.
     *
     * @param  DistanceInterface|SimilarityInterface $sim
     */
    public function similarity($sim)
    {
        if($this->getQuery() === null) {
            throw new \Exception('Please set a Query.');
        }
        if($this->getQueryModel() === null) {
            throw new \Exception('Please set Query\'s Weighting Model.');
        }

        if($this->getDocumentModel() instanceof ProbabilisticModelInterface && !$sim instanceof VectorSimilarity) {
            throw new \Exception('The document model can only be used with a VectorSimilarity type.');
        }

        $this->sim = $sim;
    }

    /**
     * get similarity. To be used for ranking documents.
     */
    public function getSimilarity()
    {
        return $this->sim;
    }

    /**
     * Get weighting model for query.
     *
     */
    public function getQueryModel()
    {
        return $this->getQuery()->getModel();
    }

    /**
     * Get document weighting model.
     *
     */
    public function getDocumentModel()
    {
        return $this->getCollection()->getModel();
    }

    /**
     * Set query. The query as document.
     *
     * @param  DocumentInterface $q
     */
    public function query(DocumentInterface $query)
    {
        $this->query = $query;
    }

    /**
     * get query.
     *
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * load index to both query and document models.
     */
    public function loadIndex()
    {
        if($this->getQuery()->getModel() === null || $this->getCollection()->getModel() === null) {
            throw new \Exception('Please apply a weighted model to query and the documents.');
        }
        $this->getQuery()->getModel()->setIndex($this->getIndex());
        $this->getCollection()->getModel()->setIndex($this->getIndex());
    }

    /**
     * Returns result ordered by rank.
     *
     * @return array
     */
    public function search()
    {

        if($this->getSimilarity() === null) {
            throw new \Exception('Please set Similarity for Ranking Documents.');
        }

        $this->loadIndex();
        
        $score = array();
        
        foreach($this->getCollection() as $class => $doc) {
            $score[$class] = $this->getScore($this->getQuery(), $doc);
        }

        arsort($score);
        return $score;

    }

    /**
     * Returns document's score against query.
     *
     * @return array
     */
    public function getScore(DocumentInterface $q, DocumentInterface $doc)
    {

        if($this->getSimilarity() instanceof SimilarityInterface){
            return $this->getSimilarity()->similarity($q, $doc);
        } elseif($this->getSimilarity() instanceof DistanceInterface){
            return $this->getSimilarity()->dist($q, $doc);
        }

    }



}