<?php

namespace Basset\Ranking\BasicModel;

use Basset\Ranking\WeightedModel;

abstract class BasicModel extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }

    abstract protected function score($tf);

    protected function idfDFR($collectionCount, $d) {
        return $this->math->DFRlog(($collectionCount+1)/($d+0.5));
    }

}