<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureExtraction;
use Basset\Index\IndexSearch;
use Basset\Index\IndexReader;
use Basset\Metric\SimilarityInterface;

/**
 * An object that wraps the Tokenized Document. It accepts $class as optional mainly for labeling purposes, 
 * otherwise it's null.
 * 
 * @see TokensDocument
 *
 * @var $feedbackdocs
 * @var $feedbackterms
 * @var $model
 * @var $query
 * @var $indexreader
 * @var $indexsearch
 *
 * @example new Document(new TokensDocument(array('how', 'do', 'you', 'do?')));
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Feedback
{


    CONST TOP_REL_DOCS = 10;

    CONST TOP_REL_TERMS = 10;

    protected $feedbackdocs;

    protected $feedbackterms;

    protected $model;

    protected $query;

    protected $indexreader;

    protected $indexsearch;

    /**
     * @param int $feedbackdocs The top documents considered to be relevant
     * @param int $feedbackterms The top terms we wish to incorporate for expansion
     */
    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS)
    {
        $this->feedbackdocs = $feedbackdocs;
        $this->feedbackterms = $feedbackterms;    
        $this->model = null;  
        $this->query = null;
    }

    /**
     * @param FeatureInterface $query
     */
    public function setQuery(FeatureInterface $query)
    {
        $this->query = $query;   
    }

    /**
     * @return FeatureInterface
     */
    public function getQuery(): FeatureInterface
    {
        return $this->query;   
    }

    /**
     * @param WeightedModelInterface $model
     */
    public function setModel(WeightedModelInterface $model)
    {
        $this->model = $model;   
    }

    /**
     * @return WeightedModelInterface
     */
    public function getModel(): WeightedModelInterface
    {
        return $this->model;   
    }

    /**
     * @param IndexReader $indexreader
     */
    public function setIndexReader(IndexReader $indexreader)
    {
        $this->indexreader = $indexreader;
        $this->indexsearch = new IndexSearch($this->indexreader);    
    }

    /**
     * Transforms vector based on given model and FeatureVector.
     *
     * @param  WeightedModelInterface $model
     * @param  FeatureVector $vector
     * @return FeatureInterface
     */
    protected function transformVector(WeightedModelInterface $model, FeatureInterface $vector): FeatureInterface
    {
        $docFeature = new FeatureExtraction($this->indexreader, $model, $vector);
        return $docFeature; 
    }

    /**
     * Score once to get initial hits, re-score again with the expanded terms for the second pass.
     *
     * @return array
     */
    public function getHits(): array
    {
        $scores = array();

        foreach($this->indexsearch->getDocumentVectors() as $class => $doc) {
            $docVector = $this->transformVector($this->getModel(), $doc);
            $scores[$class] = $this->score($this->getQuery()->getFeature(), $docVector->getFeature());
        }
        
        // just get the top N docs as specified.
        $docIds = array_slice($scores, 0, $this->feedbackdocs, true);
        $queryExpanded = $this->queryExpand($docIds);

        foreach($this->indexsearch->getDocumentVectors() as $class => $doc) {
            $docVector = $this->transformVector($this->getModel(), $doc);
            $scores[$class] = $this->score($queryExpanded->getFeature(), $docVector->getFeature());
        }

        return $scores;
    }

    /**
     * Returns document's weighted score against query.
     *
     * @param  array $a
     * @param  array $b
     * @return float
     */
    protected function score(array $a, array $b): float
    {

        if($this->getModel()->getMetric() === null) {
            throw new \Exception('Please set Similarity for Ranking Documents.');
        }

        if(!$this->getModel()->getMetric() instanceof SimilarityInterface) {
            throw new \Exception('Only SimilarityInterface allowed.');
        }

        return $this->getModel()->getMetric()->similarity($a, $b);

    }

    
}
