<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface
    };
use Basset\Models\Statistics\{
        EntryStatistics,
        CollectionStatistics
    };
use Basset\Models\{
        IBDistribution\IBDistributionInterface,
        IBLambda\IBLambdaInterface,
        Normalization\NormalizationInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermCount
    };


class IBModel extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

/**
 * Provides a framework for the family of information-based models, as described
 * in St&eacute;phane Clinchant and Eric Gaussier. 2010. Information-based
 * models for ad hoc IR. In Proceeding of the 33rd international ACM SIGIR
 * conference on Research and development in information retrieval (SIGIR '10).
 * ACM, New York, NY, USA, 234-241.
 *
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.412.7409&rep=rep1&type=pdf
 * Information-based models are obtained by instantiating the three components of the framework: 
 * selecting a ProbabilisticDistribution, selecting the lambda parameter and applying the tf normalisation.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

    protected $model;

    protected $lambda;

    protected $normalization;

    protected $index;

    public function __construct(IBDistributionInterface $model, IBLambdaInterface $lambda, NormalizationInterface $normalization)
    {
        parent::__construct();
        $this->model = $model;
        $this->lambda = $lambda;
        $this->normalization = $normalization;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
        if ($this->model === null || $this->lambda === null || $this->normalization === null) {
            throw new \Exception("Null Parameters not allowed.");
        }
    }

    public function setStats($stats)
    {
        $this->stats = $stats;
        $this->normalization->setStats($this->stats);
        $this->lambda->setStats($this->stats);
    }

    public function setCollectionStatistics(CollectionStatistics $cs)
    {
        $this->cs = $cs;
        $this->normalization->setCollectionStatistics($this->cs);
        $this->lambda->setCollectionStatistics($this->cs);
    }

    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $tf = $this->normalization->normalise($tf, $docLength);
        $lambda = $this->lambda->getLambda();

        return $this->model->score($tf, $lambda);
    }


}