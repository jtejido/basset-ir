<?php

declare(strict_types=1);

namespace Basset\Models\DFRModels;



class BE extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }


    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

    	$collectionCount = $this->getNumberOfDocuments();
    	$termFrequency = $this->getTermFrequency();

		return (
				- log(($collectionCount - 1), 2)
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