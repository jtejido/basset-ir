<?php

namespace Basset\Models\DFIModels;

use Basset\Models\WeightedModel;

class DFIModel extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }
    
    public function getExpected($doclength){

        return ($this->getTermFrequency() * $doclength) / $this->getNumberOfTokens();

    }

}