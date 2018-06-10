<?php

declare(strict_types=1);

namespace Basset\Models\DFIModels;

use Basset\Models\WeightedModel;

class DFIModel extends WeightedModel
{

	public function __construct()
    {
    	parent::__construct();
    }
    
    public function getExpected(int $doclength): float
    {

        return ($this->getTermFrequency() * $doclength) / $this->getNumberOfTokens();

    }

}