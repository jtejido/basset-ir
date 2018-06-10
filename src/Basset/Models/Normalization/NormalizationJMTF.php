<?php

declare(strict_types=1);

namespace Basset\Models\Normalization;

/**
 * We use natural Jelinek-Mercer smoothing  normalization
 */

class NormalizationJMTF extends Normalization implements NormalizationInterface
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
        $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();
        $mle_d = $tf / $docLength;
    	return ((1 - $this->lambda) * $mle_d + ($this->lambda * $mle_c)) * $docLength;
    }

}