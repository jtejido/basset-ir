<?php


namespace Basset\Expansion;

use Basset\Feature\FeatureVector;


/**
 * The contract for query expansion.
 * Ensure that the only thing it concerns about is expanding terms and not re-scoring.
 */
interface PRFInterface
{
	public function expand(FeatureVector $queryVector): FeatureVector;
}
