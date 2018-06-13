<?php


namespace Basset\Models\DFRModels;



class P extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
        $collectionCount = $this->getNumberOfDocuments();
        $termFrequency = $this->getTermFrequency();

        $f = (1 * $termFrequency) / (1 * $collectionCount);
		return ($tf * log((1 / $f), 2)
                + $f * $this->math->log2ofE()
                + 0.5 * log((2 * pi() * $tf), 2)
                + $tf * (log($tf, 2) - $this->math->log2ofE()));

	}

}