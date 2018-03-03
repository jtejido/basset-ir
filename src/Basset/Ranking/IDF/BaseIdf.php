<?php

namespace Basset\Ranking\IDF;

use Basset\Ranking\WeightedModel;


abstract class BaseIdf extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }

    abstract protected function getIdf();

}