<?php


namespace Basset\Models\Normalization;

/**
 * We use natural Jelinek-Mercer smoothing normalization using docfrequency in place of termfrequency in mle
 */

class NormalizationJMDF extends Normalization implements NormalizationInterface
{

    const C = 0.20;

	protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c = $c;
    }

    public function normalise(int $tf, int $docLength): float
    {
        $mle_c = $this->getDocumentFrequency() / $this->getNumberOfTokens();
        $mle_d = $tf / $docLength;
    	return ((1 - $this->lambda) * $mle_d + ($this->lambda * $mle_c)) * $docLength;
    }

}