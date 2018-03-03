<?php

namespace Basset\Ranking\Normalization;

/**
 * We use Term Frequency Normalisation via Pareto Distributions
 */

class NormalizationP extends Normalization implements NormalizationInterface
{

    const C = 2.20;

	protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c = $c;

    }

    public function normalise($tf, $docLength) {
        return $tf * pow($this->getAverageDocumentLength()/$docLength, $this->c);
    }

}