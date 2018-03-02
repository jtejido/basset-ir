<?php

namespace Basset\Ranking\BasicModel;



class P extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    public function score($tf){
        $collectionCount = $this->getNumberOfDocuments();
        $termFrequency = $this->getTermFrequency();

        $f = (1 * $termFrequency) / (1 * $collectionCount);
		return ($tf * $this->math->DFRlog(1 / $f)
                + $f * $this->math->log2ofE()
                + 0.5 * $this->math->DFRlog(2 * pi() * $tf)
                + $tf * ($this->math->DFRlog($tf) - $this->math->log2ofE()));

	}

}