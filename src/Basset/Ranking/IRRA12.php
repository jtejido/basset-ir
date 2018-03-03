<?php

namespace Basset\Ranking;



/**
 * An experimental IRRA system that aims to evaluate a new DFI-based term weighting model developed on the basis of
 * Shannon’s information theory (Shannon, 1949), along with the evaluation of a heuristic approach that
 * is expected to provide early precision when used together with DFI term weighting.
 * http://trec.nist.gov/pubs/trec21/papers/irra.web.nb.pdf
 * 
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IRRA12 extends SimilarityBase implements ScoringInterface
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * ∑qtf × ∆(Iij) × Λij
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @param  int $keyFrequency
     * @param  int $keylength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength)
    {
        $score = 0;

        // eij+
        $expected = (($this->getTermFrequency() +1 ) * ($docLength + 1)) / ($this->getNumberOfTokens() + 1);

        if($tf <= $expected){
            return $score;
        }
            $alpha = ($docLength - $tf) / $docLength;
            $beta = (2/3) * (($tf + 1)/$tf);
            // Λij
            $suppress_junk = pow($alpha, (3/4)) * pow($beta, (1/4));
            // ∆(Iij)
            $score += (($tf + 1) * $this->math->DFRlog(($tf + 1)/sqrt($expected))) - ($tf * $this->math->DFRlog($tf/sqrt($expected)));
            return $score * $keyFrequency * $suppress_junk;
        

    }

    
}