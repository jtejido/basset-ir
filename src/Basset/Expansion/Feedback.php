<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureExtraction;
use Basset\Index\IndexManager;
use Basset\Results\ResultSet;
use Basset\Results\ResultEntry;
use Basset\Math\Math;

/**
 * An object that is the base class for all feedback models. This should be the place where everything is set.
 * To avoid Over-fitting, we'll set this at the proven maximum recommended # of terms per documents (rel or non-rel).
 * @see http://ilpubs.stanford.edu:8090/461/1/2000-40.pdf
 *
 * @var $feedbackrelevantdocs
 * @var $feedbacknonrelevantdocs
 * @var $feedbackterms
 * @var $model
 * @var $query
 * @var $indexmanager
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Feedback
{


    CONST TOP_REL_DOCS = 20;

    CONST TOP_NON_REL_DOCS = 10;

    CONST TOP_REL_TERMS = 30;

    protected $feedbackrelevantdocs;

    protected $feedbacknonrelevantdocs;

    protected $feedbackterms;

    protected $model;

    protected $query;

    private $indexmanager;

    /**
     * @param int $feedbackdocs The top documents considered to be relevant
     * @param int $feedbacknonreldocs The bottom documents considered to be non-relevant
     * @param int $feedbackterms The top terms we wish to incorporate for expansion
     */
    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS)
    {
        $this->feedbackrelevantdocs = $feedbackdocs;
        $this->feedbacknonrelevantdocs = $feedbacknonreldocs;
        $this->feedbackterms = $feedbackterms;    
        $this->model = null;  
        $this->indexmanager = null;
        $this->results = null;
        $this->math = new Math;   
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
        $this->results = $results;
    }

    /**
     * Returns the top N results as array
     *
     * @return array
     */
    public function getRelevantDocuments(): array
    {
        $results = $this->results;
        $results->setLimit($this->feedbackrelevantdocs);
        return $results->getResults();
    }

    /**
     * Returns the bottom N results as array
     *
     * @return array
     */
    public function getNonRelevantDocuments(): array
    {
        $results = $this->results;
        $results->setOrder(1);
        $results->setLimit($this->feedbacknonrelevantdocs);
        return $results->getResults();
    }

    /**
     * Returns the last result as array
     *
     * @return array
     */
    public function getTopNonRelevantDocuments(): ResultEntry
    {
        return array_values(array_slice($this->results->getResults(), -1))[0];
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
