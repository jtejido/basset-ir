<?php


namespace Basset\Models\DFRModels;

use Basset\Models\WeightedModel;

abstract class BasicModel extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }

    abstract protected function score(int $tf, int $docLength, int $docUniqueLength): float;

}