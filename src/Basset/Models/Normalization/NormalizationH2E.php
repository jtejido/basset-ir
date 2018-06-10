<?php

declare(strict_types=1);

namespace Basset\Models\Normalization;

/**
 * We use natural log instead of log2
 */

class NormalizationH2E extends Normalization implements NormalizationInterface
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
    	return $tf * log(1 + $this->c * $this->getAverageDocumentLength() / $docLength);
    }

}