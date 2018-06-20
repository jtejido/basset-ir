<?php

namespace Basset\Search;

use Basset\Feature\{
        FeatureExtraction,
        FeatureVector,
        FeatureInterface
    };
use Basset\Index\{
        IndexReader,
        IndexSearch
    };
use Basset\Metric\{
        SimilarityInterface,
        DistanceInterface,
        MetricInterface,
        VSMInterface
    };
use Basset\Models\Contracts\{
        WeightedModelInterface,
        ProbabilisticModelInterface,
        LanguageModelInterface
    };
use Basset\{
    Documents\DocumentInterface,
    Statistics\CollectionStatistics
    };
use Basset\Expansion\{
        RelevanceModel,
        Rocchio
    };

/**
 * Search class simplifies searching of a query against a the indexed collection.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class Search
{

    CONST BETA = 0.75;

    CONST TOP_REL_DOCS = 3;

    CONST TOP_REL_TERMS = 10;

    private $indexReader;

    private $indexSearch;

    private $documentmodel;

    private $querymodel;

    private $simdist;

    private $query;

    private $queryexpansion;

    private $feedbackdocs;

    private $feedbackterms;

    /**
     * It takes an IndexReader instance for IndexSearch class.
     * IndexSearch incorporates usage of both TrieManager and IndexManager.
     *
     * @param IndexReader $indexreader
     */

    public function __construct(IndexReader $indexreader)
    {
        $this->indexReader = $indexreader;

        if($this->indexReader === null) {
            throw new \Exception("Please set an IndexReader first.");
        }
        $this->simdist = null;
        $this->query = null;
        $this->documentmodel = null;
        $this->querymodel = null;
        $this->queryexpansion = null;
        $this->indexSearch = new IndexSearch($this->indexReader);
    }

    /**
     * Set query Expansion model.
     *
     * @param PRFInterface $queryexpansion
     * @param int $fbdocs top docs to use. For Rocchio Algorithm.
     * @param int $fbterms top terms to use from top docs retrieved. For Rocchio Algorithm.
     */
    public function setQueryExpansion(bool $istrue)
    {
        $this->queryexpansion = $istrue;
    }

    /**
     * Returns the IndexSearch responsible for traversing the index and trie.
     *
     * @return IndexSearch
     */
    private function getIndexSearch(): IndexSearch
    {
        return $this->indexSearch;
    }

    /**
     * Returns an array of pre-counted classified docs.
     *
     * @return array
     */
    private function getDocumentVectors(): array
    {
        return $this->getIndexSearch()->getDocumentVectors();
    }

    /**
     * Returns an array of pre-counted classified docs.
     *
     * @return array
     */
    private function getDocumentVector(string $class): FeatureVector
    {
        return $this->getIndexSearch()->getDocumentVector($class);
    }

    /**
     * We need to set the known Collection stats from the index to each models used.
     *
     * @return CollectionStatistics
     */
    private function getCollectionStatistics(): CollectionStatistics
    {
        return $this->getIndexSearch()->getCollectionStatistics();
    }

    /**
     * Set Document's weighting model. We'll set the Collection stats at this point.
     *
     * @param  WeightedModelInterface $model
     */
    public function model(WeightedModelInterface $model)
    {
        if($this->getQuery() === null) {
            throw new \Exception("Please set a Query document first.");
        }

        $model->setCollectionStatistics($this->getCollectionStatistics());
        $this->documentmodel = $model;
    }

    /**
     * Set Query's weighting model. We'll set the Collection stats at this point.
     *
     * @param  QueryModelInterface $model
     */
    public function queryModel(WeightedModelInterface $model)
    {
        if($this->getModel() === null) {
            throw new \Exception("Please set a Model first.");
        }

        $this->getModel()->setQueryModel($model);
        $this->getModel()->getQueryModel()->setCollectionStatistics($this->getCollectionStatistics());
    }

    /**
     * Set similarity. To be used for scoring documents.
     *
     * @param  DistanceInterface|SimilarityInterface $sim
     */
    public function similarity(MetricInterface $metric)
    {
        if($this->getModel() === null) {
            throw new \Exception("Please set a Model first.");
        }

        if($this->getModel() instanceof ProbabilisticModelInterface && $metric instanceof VSMInterface) {
            throw new \Exception("You cannot use similarity of VSMInterface type to a ProbabilisticModelInterface type.");
        }

        if(!$this->getModel() instanceof ProbabilisticModelInterface && !$metric instanceof VSMInterface) {
            throw new \Exception("You cannot use similarity of a non-VSMInterface type to a non-ProbabilisticModelInterface type.");
        }

        $this->getModel()->setMetric($metric);
    }

    /**
     * To be used for scoring documents.
     *
     * @return MetricInterface
     */
    public function getSimilarity(): MetricInterface
    {
        return $this->getModel()->getMetric();
    }

    /**
     * Get weighting model for query.
     *
     * @return WeightedModelInterface
     */
    public function getQueryModel(): WeightedModelInterface
    {
        if($this->getModel() === null) {
            throw new \Exception("Please set a Model first.");
        }

        if($this->getSimilarity() instanceof VSMInterface) {
            return $this->getModel();
        } else {
            return $this->getModel()->getQueryModel();
        }
    }

    /**
     * Get document weighting model.
     *
     * @return WeightedModelInterface
     */
    public function getModel(): WeightedModelInterface
    {

        if($this->documentmodel === null) {
            throw new \Exception("Please set a Model first.");
        }
        return $this->documentmodel;
    }

    /**
     * Set query. The query as instance of FeatureVector of precounted query tokens.
     *
     * @param  DocumentInterface $q
     */
    public function query(DocumentInterface $query)
    {
        $this->query = new FeatureVector(array_count_values($query->getDocument()));
    }

    /**
     * Returns the FeatureVector instance of precounted query tokens.
     *
     * @return FeatureVector
     */
    public function getQuery(): FeatureVector
    {
        return $this->query;
    }

    /**
     * Returns result ordered by rank. Limited by $limit specified.
     * This also transforms both query and document's feature vector.
     *
     * @param  int $limit
     * @return array
     */
    public function search(int $limit = 10, bool $descending = true): array
    {
        
        $scores = array();
        $queryVector = $this->transformVector($this->getQueryModel(), $this->getQuery());
        // at this point, any changes in query model and metric should be set in the model.
        if($this->queryexpansion) {
            if($this->getModel() instanceof LanguageModelInterface) {
                $expansion = new RelevanceModel;
            } else {
                 $expansion = new Rocchio;
            }

            $expansion->setModel($this->getModel());
            $expansion->setQuery($this->getQuery());
            $expansion->setIndexReader($this->indexReader);
            $scores = $expansion->getHits();
        } else {
            $scores = $this->getHits($queryVector);
        }

        if ($descending) {
            arsort($scores);
        } else {
            asort($scores);
        }

        return array_slice($scores, 0, $limit, true);

    }

    /**
     * Expands original query based on array of relevant docs received.
     *
     * @param  array $docIds
     * @return array
     */
    private function queryExpand(array $docIds): FeatureInterface
    {

        $relevantVector = new FeatureVector;

        // re-weight the query to match that of relevant docs
        $queryVector = $this->transformVector($this->getModel(), $this->getQuery())->getFeature(); 

        /**
         * Rocchio's algorithm reduces the weight from the docs' terms.
         */
        foreach($docIds as $class) {
            $doc = $this->getDocumentVector($class);
                $docVector = $this->transformVector($this->getModel(), $doc)->getFeature(); 
                array_walk_recursive($docVector, function (&$item, $key) 
                    {
                        $item *= self::BETA / $this->feedbackdocs;
                    }
                );
                $relevantVector->addTerms($docVector);
        }

        // combine the new terms with the original query
        $relevantVector->addTerms($queryVector);
        $relevantVector->snip($this->feedbackterms);

        // we just need the top N of new query
        return $relevantVector;

    }

    /**
     * Transforms vector based on given model and FeatureVector.
     *
     * @param  WeightedModelInterface $model
     * @param  FeatureVector $vector
     * @return array
     */
    private function transformVector(WeightedModelInterface $model, FeatureInterface $vector): FeatureInterface
    {
        $docFeature = new FeatureExtraction($this->indexReader, $model, $vector);
        return $docFeature; 
    }

    /**
     * Gets document hits based on given query
     *
     * @param  bool $descending
     * @param  FeatureVector $queryVector
     * @return float
     */
    private function getHits(FeatureInterface $queryVector): array
    {
        
        $scores = array();

        foreach($this->getDocumentVectors() as $class => $doc) {
            $docVector = $this->transformVector($this->getModel(), $doc);
            $scores[$class] = $this->score($queryVector->getFeature(), $docVector->getFeature());
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
    private function score(array $a, array $b): float
    {

        if($this->getSimilarity() === null) {
            throw new \Exception('Please set Similarity for Ranking Documents.');
        }

        if($this->getSimilarity() instanceof SimilarityInterface){
            return $this->getSimilarity()->similarity($a, $b);
        } elseif($this->getSimilarity() instanceof DistanceInterface){
            return $this->getSimilarity()->dist($a, $b);
        }

    }



}