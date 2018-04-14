<?php

namespace Basset\Models;

use Basset\Models\Contracts\WeightedModelInterface;

/**
 * AKA Pivoted Normalization Weighting.
 * Returns a modified tf with pivot length normalization and Robertson-Sparck IDF as described in 
 * Singhal et al., 1996.
 * https://www.csee.umbc.edu/~nicholas/676/papers/p21-singhal.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class PivotedTfIdf extends WeightedModel implements WeightedModelInterface
{


    protected $slope;

    const SLOPE = 0.20;

    public function __construct($slope = self::SLOPE)
    {
        $this->slope = $slope;
    }


    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        return (1+log(1+log($tf))) / ((1-$this->slope) + ($this->slope * ($docLength / $this->getAverageDocumentLength()))) * log(($this->getNumberOfDocuments()+1)/$this->getDocumentFrequency());

    }


}