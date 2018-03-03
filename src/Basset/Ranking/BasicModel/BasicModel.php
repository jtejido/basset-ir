<?php

namespace Basset\Ranking\BasicModel;

use Basset\Ranking\WeightedModel;

abstract class BasicModel extends WeightedModel
{

    abstract protected function score($tf);

    protected function idfDFR($collectionCount, $d) {
        return $this->math->DFRlog(($collectionCount+1)/($d+0.5));
    }

}