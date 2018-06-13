<?php


namespace Basset\Models\DFRModels;



class InExp extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }


    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $collectionCount = $this->getNumberOfDocuments();
        $termFrequency = $this->getTermFrequency();

        $f = $termFrequency / $collectionCount;
        $n_exp = $collectionCount * (1 - exp(-$f));
        $idf = log((($this->getNumberOfDocuments()+1)/($this->getDocumentFrequency()+0.5)), 2);
		return $tf * $idf;

	}

}