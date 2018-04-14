<?php

namespace Basset\Models;

use Basset\Index\IndexInterface;
use Basset\Models\DFRModels\BasicModelInterface;
use Basset\Models\DFRAfterEffect\AfterEffectInterface;
use Basset\Models\Normalization\NormalizationInterface;
use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;


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
        if ($this->model == null || $this->aftereffect == null || $this->normalization == null) {
            throw new \Exception("Null Parameters not allowed.");
        }
    }

    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;
        $this->normalization->setIndex($this->index);
        $this->aftereffect->setIndex($this->index);
        $this->model->setIndex($this->index);
    }

    public function score($tf, $docLength, $docUniqueLength)
    {

        $tf = $this->normalization->normalise($tf, $docLength); 
        $gain = $this->aftereffect->gain($tf);

        return $gain * $this->model->score($tf, $docLength, $docUniqueLength);
    }


}