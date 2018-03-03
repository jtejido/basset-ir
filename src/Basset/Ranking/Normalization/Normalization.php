<?php

namespace Basset\Ranking\Normalization;

use Basset\Ranking\WeightedModel;

abstract class Normalization extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }

    abstract protected function normalise($tf, $docLength);

}