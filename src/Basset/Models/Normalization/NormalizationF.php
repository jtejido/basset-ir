<?php


namespace Basset\Models\Normalization;

/**
 * We use increasing density function for the frequency normalisation..
 */

class NormalizationF extends Normalization implements NormalizationInterface
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
    	return $tf * ($this->c * $docLength / $this->getAverageDocumentLength());
    }

}