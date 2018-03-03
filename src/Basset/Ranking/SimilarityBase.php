<?php

namespace Basset\Ranking;


/**
 *
 * This class should be extended by Scoring types
 *
 */

abstract class SimilarityBase extends WeightedModel
{


    public function __construct()
    {
    	parent::__construct();
    }

    abstract protected function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength);

}