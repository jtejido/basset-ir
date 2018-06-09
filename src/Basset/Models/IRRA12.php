<?php

namespace Basset\Models;

use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Metric\VectorSimilarity;
use Basset\Models\TermCount;

/**
 * An experimental IRRA system that aims to evaluate a new DFI-based term weighting model developed on the basis of
 * Shannon’s information theory (Shannon, 1949), along with the evaluation of a heuristic approach that
 * is expected to provide early precision when used together with DFI term weighting.
 * http://trec.nist.gov/pubs/trec21/papers/irra.web.nb.pdf
 * 
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IRRA12 extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }


    /**
     * ∑qtf × ∆(Iij) × Λij
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {
        $score = 0;

        // eij+
        $expected = ($this->getTermFrequency() * $docLength) / $this->getNumberOfTokens();
        $expected_plus = (($this->getTermFrequency() +1 ) * ($docLength + 1)) / ($this->getNumberOfTokens() + 1);

        if($tf <= $expected){
            return $score;
        }
            $alpha = ($docLength - $tf) / $docLength;
            $beta = (2/3) * (($tf + 1)/$tf);
            // Λij
            $suppress_junk = pow($alpha, (3/4)) * pow($beta, (1/4));
            // ∆(Iij)
            $score += (($tf + 1) * log((($tf + 1)/sqrt($expected_plus)), 2)) - ($tf * log(($tf/sqrt($expected)), 2));
            return $score  * $suppress_junk;
        

    }

    
}