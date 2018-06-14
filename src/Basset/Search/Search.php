<?php


namespace Basset\Search;

use Basset\FeatureExtraction\FeatureExtraction;
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
        ProbabilisticModelInterface
    };
use Basset\{
    Documents\DocumentInterface,
    Statistics\CollectionStatistics
    };

/**
 * Search class simplifies searching of a query against a the indexed collection.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class Search
{

    private $indexReader;

    private $indexSearch;

    private $documentmodel;

    private $querymodel;

    private $simdist;

    private $query;

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
        $this->indexSearch = new IndexSearch($this->indexReader);
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
    private function getDocuments(): array
    {
        return $this->getIndexSearch()->getDocuments();
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
     * Set query. The query as precounted vector.
     *
     * @param  DocumentInterface $q
     */
    public function query(DocumentInterface $query)
    {
        $this->query = array_count_values($query->getDocument());
    }

    /**
     * Returns the precounted query tokens.
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Returns result ordered by rank. Limited by $limit specified.
     * This also assembles both the query and document's feature vector.
     *
     * @param  int $limit
     * @return array
     */
    public function search($limit = 10, $descending = true): array
    {
        
        $score = array();
        foreach($this->getDocuments() as $class => $doc) {
            $docFeature = new FeatureExtraction($this->indexReader, $this->getModel());
            $queryFeature = new FeatureExtraction($this->indexReader, $this->getQueryModel());
            $score[$class] = $this->score($docFeature->getFeature($doc), $queryFeature->getFeature($this->getQuery()));
        }

        if ($descending) {
            arsort($score);
        } else {
            asort($score);
        }
        
        return array_slice($score, 0, $limit, true);

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