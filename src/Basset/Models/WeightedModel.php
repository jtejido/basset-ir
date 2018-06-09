<?php

namespace Basset\Models;

use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;
use Basset\Index\IndexInterface;
use Basset\Models\Normalization\NormalizationInterface;
use Basset\Models\AfterEffect\AfterEffectInterface;
use Basset\Math\Math;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Metric\MetricInterface;

abstract class WeightedModel
{


    private $stats;

    private $cs;

    protected $math;

    protected $queryModel;

    protected $metric;

    CONST E = M_E;

    public function __construct()
    {
        $this->math = new Math();
    }

    public function setMetric(MetricInterface $metric)
    {
        $this->metric = $metric;
    }

    public function getMetric(): MetricInterface
    {
        return $this->metric;
    }

    public function setQueryModel(WeightedModelInterface $model)
    {
        $this->queryModel = $model;
    }

    public function getQueryModel(): WeightedModelInterface
    {
        return $this->queryModel;
    }

    public function setStats(EntryStatistics $stats)
    {
        $this->stats = $stats;
    }

    public function setCollectionStatistics(CollectionStatistics $cs)
    {
        $this->cs = $cs;
    }

    public function getCollectionStatistics(): CollectionStatistics
    {
        return $this->cs;
    }

    protected function getTermFrequency(): int
    {
        return $this->stats->getTermFrequency();
    }

    protected function getDocumentFrequency(): int
    {
        return $this->stats->getDocumentFrequency();
    }

    protected function getAverageDocumentLength(): float
    {
        return $this->getCollectionStatistics()->getAverageDocumentLength();
    }

    protected function getNumberOfTokens(): int
    {
        return $this->getCollectionStatistics()->getNumberOfTokens();
    }

    protected function getNumberOfUniqueTerms(): int
    {
        return $this->getCollectionStatistics()->getNumberOfUniqueTokens();
    }

    protected function getNumberOfDocuments(): int
    {
        return $this->getCollectionStatistics()->getNumberOfDocuments();
    }

    protected function getTotalByTermPresence(): int
    {
        return $this->stats->getTotalByTermPresence();
    }

    protected function getUniqueTotalByTermPresence(): int
    {
        return $this->stats->getUniqueTotalByTermPresence();
    }

    public function getScore($tf, $docLength, $docUniqueLength)
    {
        return $this->score($tf, $docLength, $docUniqueLength);
    }


}