<?php

namespace Basset\Models\Normalization;

use Basset\Models\WeightedModel;

abstract class Normalization extends WeightedModel
{
	public function __construct()
    {
    	parent::__construct();
    }
    
    abstract protected function normalise($tf, $docLength);

}