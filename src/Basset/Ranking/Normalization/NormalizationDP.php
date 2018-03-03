<?php

namespace Basset\Ranking\Normalization;

/**
 * We use natural Dirichlet Priors normalization
 */

class NormalizationDP extends Normalization implements NormalizationInterface
{

    const C = 2500;

	protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c = $c;

    }

    public function normalise($tf, $docLength) {
        $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();
    	return $this->c * ($tf + $this->c * $mle_c) / ($docLength + $this->c);
    }

}