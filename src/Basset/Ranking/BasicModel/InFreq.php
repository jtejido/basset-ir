<?php

namespace Basset\Ranking\BasicModel;



class InFreq extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    public function score($tf){

        $idf = $this->idfDFR($this->getNumberOfDocuments(), $this->getTermFrequency());
		return $idf * $tf;

	}

}