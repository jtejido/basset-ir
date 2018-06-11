<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Statistics\{
        EntryStatistics,
        CollectionStatistics
    };
use Basset\Models\{
        Normalization\NormalizationInterface,
        AfterEffect\AfterEffectInterface,
        Contracts\WeightedModelInterface
    };
use Basset\{
        Index\IndexInterface,
        Math\Math,
        Metric\MetricInterface
    };
    
/**
 * All properties are set here. This acts as the base for all models of different kinds(VSM, Probabilistic, etc).
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

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

    public function getScore($tf, $docLength, $docUniqueLength): float
    {
        return $this->score($tf, $docLength, $docUniqueLength);
    }


}