<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermCount
    };


/**
 * XSqrAM is a class that computes the inner product of Pearson's X^2 with the information growth computed 
 * with the multinomial M.
 *
 * @see Frequentist and Bayesian approach to  Information Retrieval. G. Amati. In Proceedings of the 28th European Conference on IR Research (ECIR 2006). LNCS vol 3936, pages 13--24.
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class XSqrAM extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $mle_d = $tf/$docLength;

        $smoothedProbability = ($tf + 1)/($docLength + 1);

        $mle_c = $this->getTermFrequency()/$this->getNumberOfTokens();

        $XSqrA = pow(1-$mle_d,2)/($tf+1);  

        $InformationDelta =  ($tf+1) * log($smoothedProbability/$mle_c) - $tf*log($mle_d /$mle_c) +0.5*log($smoothedProbability/$mle_d);

        return $tf * $XSqrA * $InformationDelta;

    }

}