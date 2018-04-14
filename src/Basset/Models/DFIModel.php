<?php

namespace Basset\Models;

use Basset\Index\IndexInterface;
use Basset\Models\Contracts\IDFInterface;
use Basset\Models\DFIModels\DFIInterface;
use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;

class DFIModel extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

/**
 * Provides a framework for nonparametric index term weighting model using the notion of independence, as described
 * in Kocabas et al's paper.
 * http://trec.nist.gov/pubs/trec18/papers/muglau.WEB.MQ.pdf
 *
 * DFI models are obtained by instantiating the three components of the framework: 
 * selecting a divergence measure and selecting an idf method, take note that it uses log2 similar to DFR.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

    protected $model;

    protected $idf;

    protected $index;

    public function __construct(DFIInterface $model, IDFInterface $idf)
    {
        parent::__construct();
        $this->model = $model;
        $this->idf = $idf;
        if ($this->model == null || $this->idf == null) {
            throw new \Exception("Null Parameters not allowed.");
        }
    }

    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;
        $this->model->setIndex($this->index);
        $this->idf->setIndex($this->index);
    }

    public function score($tf, $docLength, $docUniqueLength)
    {
        $this->idf->setBase(2); // because the paper uses base 2 for all idf
        $dfi = $this->model->score($tf, $docLength, $docUniqueLength); 
        $idf = $this->idf->score($tf, $docLength, $docUniqueLength);

        return $dfi * $idf;
    }


}