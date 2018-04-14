<?php

namespace Basset\Models;

use Basset\Models\Contracts\TFInterface;
use Basset\Models\Contracts\WeightedModelInterface;

/**
 * PivotedConcaveTFIDF (renamed original name of (TF)l◦δ◦p due to its eccentricity for a class name) is 
 * another variant of TF following Rousseau & Vazirgiannis' work by rewriting the log-concavity TF with
 * Pivoted TF.
 *
 * Composition of TF Normalizations: New Insights on Scoring Functions for Ad Hoc IR, SIGIR 2013, p. 917-920.
 * Rousseau & Vazirgiannis
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class PivotedConcaveTF extends WeightedModel implements WeightedModelInterface, TFInterface
{

    const B = 0.20;

    const D = 0.5;

    protected $b;

    protected $d;

    public function __construct($b = self::B, $d = self::D)
    {
        parent::__construct();
        $this->d = $d;
        $this->b = $b;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        $num = $tf;
        $denom = 1 - $this->b + $this->b * ($docLength / $this->getAverageDocumentLength());
        
        return (1+log(1+log(($num/$denom) + $this->d)));

    }

}