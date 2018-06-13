<?php


namespace Basset\Models\DFRModels;



class In extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }


    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $idf = log((($this->getNumberOfDocuments()+1)/($this->getDocumentFrequency()+0.5)), 2);
		return $tf * $idf;

	}

}