<?php

declare(strict_types=1);

namespace Basset\Models\Normalization;

/**
 * The density function of the term frequency is inversely proportional to the length.
 */

class NormalizationH2 extends Normalization implements NormalizationInterface
{

    const C = 1;

	protected $c;

    public function __construct($c = self::C)
    {
    	parent::__construct();
        $this->c = $c;
    }

    public function normalise(int $tf, int $docLength): float
    {
    	return $tf * $this->math->DFRlog(1 + $this->c * $this->getAverageDocumentLength() / $docLength);
    }

}