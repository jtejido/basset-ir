<?php

namespace Basset\Ranking\BasicModel;



class InExp extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }


    public function score($tf){

        $collectionCount = $this->getNumberOfDocuments();
        $termFrequency = $this->getTermFrequency();

        $f = $termFrequency / $collectionCount;
        $n_exp = $collectionCount * (1 - exp(-$f));
        $idf = $this->idfDFR($collectionCount, $n_exp);
		return $tf * $idf;

	}

}