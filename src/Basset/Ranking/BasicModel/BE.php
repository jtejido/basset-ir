<?php

namespace Basset\Ranking\BasicModel;



class BE extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }


    public function score($tf){

    	$collectionCount = $this->getNumberOfDocuments();
    	$termFrequency = $this->getTermFrequency();

		return (
				- $this->math->DFRlog($collectionCount - 1)
				- $this->math->log2ofE()
				+ $this->math->stirlingPower(
					$collectionCount
						+ $termFrequency
						- 1,
					$collectionCount
						+ $termFrequency
						- $tf
						- 2)
				- $this->math->stirlingPower($termFrequency, $termFrequency - $tf)
			);
	}

}