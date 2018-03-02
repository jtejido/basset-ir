<?php

namespace Basset\Ranking\BasicModel;



class G extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    public function score($tf){

        $collectionCount = $this->getNumberOfDocuments();
        $termFrequency = $this->getTermFrequency();

    	$F = $termFrequency + 1;
    	$lambda = $F / ($collectionCount + $F);
        $A = $this->math->DFRlog($lambda + 1);
        $B = $this->math->DFRlog((1 + $lambda) / $lambda);

		return ($B - ($B - $A) / (1 + $tf)) ;

	}

}