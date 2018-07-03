<?php

namespace Basset\Search;

use Basset\Feature\{
        FeatureExtraction,
        FeatureVector,
        FeatureInterface
    };
use Basset\Index\{
        IndexReader,
        IndexManager
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
        PRFInterface,
        RelevanceModel,
        PRFVSMInterface
    };
use Basset\Results\{
        ResultEntry,
        ResultSet
    };

/**
 * Search class manages searching of a query against the indexed collection.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class Search
{

    private $indexReader;

    private $indexManager;

    private $documentmodel;

    private $query;

    private $queryexpansion;

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
        $this->query = null;
        $this->documentmodel = null;
        $this->queryexpansion = null;
        $this->indexManager = $this->indexReader->getIndexManager();
    }

    /**
     * Set query Expansion model.
     *
     * @param PRFInterface $queryExpansion
     */
    public function setQueryExpansion(PRFInterface $queryExpansion)
    {
        if(($this->getModel() instanceof LanguageModelInterface && !$queryExpansion instanceof RelevanceModel) || (!$this->getModel() instanceof LanguageModelInterface && $queryExpansion instanceof RelevanceModel)) {
            throw new \Exception("Only LanguageModelInterface supports RelevanceModel.");
        }

        if(!$this->getModel() instanceof LanguageModelInterface && !$queryExpansion instanceof PRFVSMInterface) {
            throw new \Exception("Vector Space and Probabilistic models only support PRFVSMInterface.");
        }

        $this->queryexpansion = $queryExpansion;
    }

    /**
     * @return PRFInterface
     */
    public function getQueryExpansion(): PRFInterface
    {
        return $this->queryexpansion;
    }

    /**
     * Returns the IndexSearch responsible for traversing the index and trie.
     *
     * @return IndexSearch
     */
    private function getIndexManager(): IndexManager
    {
        return $this->indexManager;
    }

    /**
     * Returns an array of pre-counted classified docs.
     *
     * @return array
     */
    private function getDocumentVectors(): array
    {
        return $this->getIndexManager()->getDocumentVectors();
    }

    /**
     * Returns an array of pre-counted classified docs.
     *
     * @return array
     */
    private function getDocumentVector(int $id): FeatureVector
    {
        return $this->getIndexManager()->getDocumentVector($id);
    }

    /**
     * We need to set the known Collection stats from the index to each models used.
     *
     * @return CollectionStatistics
     */
    private function getCollectionStatistics(): CollectionStatistics
    {
        return $this->getIndexManager()->getCollectionStatistics();
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
    public function search(int $limit = 10, bool $descending = true): ResultSet
    {

        $queryVector = $this->transformVector($this->getQueryModel(), $this->getQuery());

        $results = $this->getResults($queryVector);
        
        /*
         * At this point, any changes in query model and metric should be set in the model already.
         * We already have the initial results, and if relevance feedback is set to true, we'll do query expansion.
         */

        if($this->queryexpansion !== null) {
            if($this->getQueryExpansion() instanceof Rocchio) {
                $queryVector = $this->transformVector($this->getModel(), $this->getQuery());
            }

            $this->getQueryExpansion()->setModel($this->getModel());
            $this->getQueryExpansion()->setIndexManager($this->getIndexManager());
            $this->getQueryExpansion()->setResults($results);
            $queryVector = $this->getQueryExpansion()->expand($queryVector);
            $results = $this->getResults($queryVector);
        }

        if (!$descending) {
            $results->setOrder(1);
        }

        $results->setLimit($limit);

        return $results;

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
        $docFeature = new FeatureExtraction($this->getIndexManager(), $model, $vector);
        return $docFeature; 
    }

    /**
     * Gets document hits based on given query
     *
     * @return ResultSet
     */
    private function getResults(FeatureInterface $queryVector): ResultSet
    {
        

        $results = new ResultSet;

        foreach($this->getDocumentVectors() as $id => $doc) {
            $docVector = $this->transformVector($this->getModel(), $doc);
            $score = $this->score($queryVector->getFeature(), $docVector->getFeature());
            $results->addEntry(new ResultEntry($id, $score, $this->getIndexManager()->getMetaData($id)));
        }

        return $results;

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