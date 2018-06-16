<?php


namespace Basset\Search;

use Basset\FeatureExtraction\{
        FeatureExtraction,
        FeatureVector
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

    CONST ALPHA = 1;

    CONST BETA = 0.8;

    CONST TOP_REL_DOCS = 30;

    CONST TOP_REL_TERMS = 20;

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
        $this->queryexpansion = false;
        $this->feedbackdocs = 0;
        $this->feedbackterms = 0;
        $this->indexSearch = new IndexSearch($this->indexReader);
    }

    /**
     * Set query Expansion model.
     *
     * @param bool $queryexpansion
     * @param int $fbdocs top docs to use. For Rocchio Algorithm.
     * @param int $fbterms top terms to use from top docs retrieved. For Rocchio Algorithm.
     */
    public function setQueryExpansion(bool $queryexpansion = false, int $fbdocs = self::TOP_REL_DOCS, int $fbterms = self::TOP_REL_TERMS)
    {
        $this->queryexpansion = $queryexpansion;

        if ($this->queryexpansion) {
            $this->feedbackdocs = $fbdocs;
            $this->feedbackterms = $fbterms;
        }
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

        $queryFeature = new FeatureExtraction($this->indexReader, $this->getQueryModel(), $this->getQuery());
        $queryVector = $queryFeature->getFeature();

        $scores = $this->getHits($descending, $queryVector);

        if($this->queryexpansion) {
            $scores = $this->reScore($descending, array_slice($scores, 0, $this->feedbackdocs, true));
        }

        return array_slice($scores, 0, $limit, true);

    }

    private function reScore(bool $descending = true, array $scores): array
    {
        
        $relevantDocs = $scores;

        $relevantVector = new FeatureVector;
        foreach($relevantDocs as $class => $score) {
            $docVector = $this->getDocumentVector($class)->getFeature();
            foreach($docVector as $term => $weight) {
                $relevantVector->addTerm($term, $weight * self::BETA / $this->feedbackdocs);
            }
        }
        $queryFeature = new FeatureExtraction($this->indexReader, $this->getModel(), $this->getQuery());
        $queryVector = $queryFeature->getFeature();

        foreach($queryVector as $term => $weight) {
            $relevantVector->addTerm($term, $weight * self::ALPHA);
        }

        $scores = $this->getHits($descending, $relevantVector->clip($this->feedbackterms));

        return $scores;

    }

    private function getHits(bool $descending = true, array $queryVector): array
    {
        
        $scores = array();

        foreach($this->getDocumentVectors() as $class => $doc) {
            $docFeature = new FeatureExtraction($this->indexReader, $this->getModel(), $doc);
            $docVector = $docFeature->getFeature(); 
            $scores[$class] = $this->score($docVector, $queryVector);
        }

        if ($descending) {
            arsort($scores);
        } else {
            asort($scores);
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