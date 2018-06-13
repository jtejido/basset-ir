<?php


namespace Basset\Models\DFRAfterEffect;

use Basset\Models\WeightedModel;

abstract class AfterEffect extends WeightedModel
{
  
	public function __construct()
    {
    	parent::__construct();
    }
      
    abstract protected function gain(int $tf): float;

}