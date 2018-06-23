<?php


namespace Basset\Expansion;

use Basset\Feature\FeatureInterface;


/**
 * The contract for query expansion.
 * Ensure that the only thing it concerns about is expanding terms and not re-scoring.
 */
interface PRFInterface
{
	public function expand(FeatureInterface $queryVector): FeatureInterface;
}
