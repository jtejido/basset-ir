<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureExtraction;
use Basset\Index\IndexManager;
use Basset\Results\ResultSet;

/**
 * An object that is the base class for all feedback models. This should be the place where everything is set.
 * 
 * @see TokensDocument
 *
 * @var $feedbackdocs
 * @var $feedbackterms
 * @var $model
 * @var $query
 * @var $indexmanager
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

    private $indexmanager;

    /**
     * @param int $feedbackdocs The top documents considered to be relevant
     * @param int $feedbackterms The top terms we wish to incorporate for expansion
     */
    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS)
    {
        $this->feedbackdocs = $feedbackdocs;
        $this->feedbackterms = $feedbackterms;    
        $this->model = null;  
        $this->indexmanager = null;
        $this->results = null;   
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
     * @param IndexManager $indexmanager
     */
    public function setIndexManager(IndexManager $indexmanager)
    {
        $this->indexmanager = $indexmanager;
    }

    /**
     * @return IndexManager
     */
    public function getIndexManager(): IndexManager
    {
        return $this->indexmanager;
    }

    /**
     * The top N results.
     *
     * @param ResultSet $results
     */
    public function setResults(ResultSet $results)
    {
        $results->setLimit($this->feedbackdocs);
        $this->results = $results;
    }

    /**
     * Returns the top N results as array
     *
     * @return array
     */
    public function getResults(): array
    {
        return $this->results->getResults();
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
        $docFeature = new FeatureExtraction($this->getIndexManager(), $model, $vector);
        return $docFeature; 
    }

    
}
