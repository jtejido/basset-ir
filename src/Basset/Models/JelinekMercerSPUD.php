<?php

namespace Basset\Models;

use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;

/**
 * JelinekMercerSPUD is a class for ranking documents that captures the tendency of a term to repeat itself within
 * a document (i.e. word burstiness).
 *
 * From Ronan Cummins et al. 2015. A Polya Urn Document Language Model for Improved Information Retrieval.
 * https://arxiv.org/pdf/1502.00804.pdf
 * The optimal for μ appears to have a wide range (500-10000).
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class JelinekMercerSPUD extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Estimation of the Background DCM (EDCM) via Multivariate Polya Distribution
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength)
    {
        $score = 0;

        if($tf > 0){
            $lambda = $docUniqueLength/$docLength;
            $mle = $tf/$docLength;
            $score += $keyFrequency * log( 1 + ((1-$lambda) * $mle + $lambda * ($this->getDocumentFrequency()/$this->getUniqueTotalByTermPresence())));
        }

        return $score;

    }

    
}