<?php

namespace Basset\Ranking\AfterEffect;

use Basset\Ranking\WeightedModel;

abstract class AfterEffect extends WeightedModel
{
  
	public function __construct()
    {
    	parent::__construct();
    }
      
    abstract protected function gain($tf);

}