<?php

namespace Basset\Ranking;


/**
 *
 * This class should be extended by Scoring types
 *
 */

abstract class SimilarityBase extends WeightedModel
{

    abstract protected function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength);

}