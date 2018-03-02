<?php

namespace Basset\Ranking\BasicModel;



class In extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }


    public function score($tf){

        $idf = $this->idfDFR($this->getNumberOfDocuments(), $this->getDocumentFrequency());
		return $tf * $idf;

	}

}