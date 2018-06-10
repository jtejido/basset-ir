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
    DFRModels\BasicModelInterface,
    DFRAfterEffect\AfterEffectInterface,
    Normalization\NormalizationInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermCount
    };


class DFRModel extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

/**
 * DFR is a framework for ranking documents against a query based on Harter's 2-Poisson index-model.
 * S.P. Harter. A probabilistic approach to automatic keyword indexing. PhD thesis, Graduate Library, The University of
 * Chicago, Thesis No. T25146, 1974
 * This class provides an alternative way of specifying an arbitrary DFR weighting model, by mixing the used components.
 *
 * The implementation is strictly based on G. Amati, C. Rijsbergen paper:
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.97.8274&rep=rep1&type=pdf
 *
 * DFR models are obtained by instantiating the three components of the framework: 
 * selecting a basic randomness model, applying the first normalisation and normalising the term frequencies.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

    protected $model;

    protected $aftereffect;

    protected $normalization;

    protected $index;

    public function __construct(BasicModelInterface $model, AfterEffectInterface $aftereffect, NormalizationInterface $normalization)
    {
        parent::__construct();
        $this->model = $model;
        $this->aftereffect = $aftereffect;
        $this->normalization = $normalization;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
        if ($this->model == null || $this->aftereffect == null || $this->normalization == null) {
            throw new \Exception("Null Parameters not allowed.");
        }
    }

    public function setStats(EntryStatistics $stats)
    {
        $this->stats = $stats;
        $this->normalization->setStats($this->stats);
        $this->aftereffect->setStats($this->stats);
        $this->model->setStats($this->stats);
    }

    public function setCollectionStatistics(CollectionStatistics $cs)
    {
        $this->cs = $cs;
        $this->normalization->setCollectionStatistics($this->cs);
        $this->aftereffect->setCollectionStatistics($this->cs);
        $this->model->setCollectionStatistics($this->cs);
    }

    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $tf = $this->normalization->normalise($tf, $docLength); 
        $gain = $this->aftereffect->gain($tf);

        return $gain * $this->model->score($tf, $docLength, $docUniqueLength);
    }


}