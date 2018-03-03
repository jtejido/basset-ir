<?php

namespace Basset\Ranking\IBLambda;

use Basset\Ranking\WeightedModel;

abstract class Lambda extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }
    
    abstract protected function getLambda();

}