<?php

namespace Basset\Ranking;


/**
 * XSqrA_M is a class that implements the XSqrA_M weighting model which computed the 
 * inner product of Pearson's X^2 with the information growth computed 
 * with the multinomial M.
 *
 * Frequentist and Bayesian approach to  Information Retrieval. G. Amati. In 
 * Proceedings of the 28th European Conference on IR Research (ECIR 2006). 
 * LNCS vol 3936, pages 13--24.
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class XSqrA_M extends SimilarityBase implements ScoringInterface
{

    /**
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

        if($tf > 0){
            $mle_d = $tf/$docLength;

            $smoothedProbability = ($tf + 1)/($docLength + 1);

            $mle_c = $this->getTermFrequency()/$this->getNumberOfTokens();

            $XSqrA = pow(1-$mle_d,2)/($tf+1);  

            $InformationDelta =  ($tf+1) * log($smoothedProbability/$mle_c) - $tf*log($mle_d /$mle_c) +0.5*log($smoothedProbability/$mle_d);

            $score += $keyFrequency * $tf * $XSqrA * $InformationDelta;
        }

        return $score;

    }

}