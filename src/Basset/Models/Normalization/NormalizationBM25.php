<?php


namespace Basset\Models\Normalization;

/**
 * We use natural BM25's normalization
 */

class NormalizationBM25 extends Normalization implements NormalizationInterface
{

    const C = 0.75;

	protected $c;

    public function __construct($c = self::C)
    {
    	parent::__construct();
        $this->c = $c;
    }

    public function normalise(int $tf, int $docLength): float
    {
    	return $tf / (1 - $this->c + $this->c * ($docLength / $this->getAverageDocumentLength()));
    }

}