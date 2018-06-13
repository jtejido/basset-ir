<?php


namespace Basset\Models\DFRModels;



class G extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $collectionCount = $this->getNumberOfDocuments();
        $termFrequency = $this->getTermFrequency();

    	$F = $termFrequency + 1;
    	$lambda = $F / ($collectionCount + $F);
        $A = log(($lambda + 1), 2);
        $B = log(((1 + $lambda) / $lambda), 2);

		return ($B - ($B - $A) / (1 + $tf)) ;

	}

}