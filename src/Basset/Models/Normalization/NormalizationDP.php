<?php


namespace Basset\Models\Normalization;

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

    public function normalise(int $tf, int $docLength): float
    {
        $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();
    	return $this->c * ($tf + $this->c * $mle_c) / ($docLength + $this->c);
    }

}