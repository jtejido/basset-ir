<?php


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
 * JelinekMercerSPUD is a class for ranking documents that captures the tendency of a term to repeat itself within
 * a document (i.e. word burstiness).
 *
 * From Ronan Cummins et al. 2015. A Polya Urn Document Language Model for Improved Information Retrieval.
 * @see https://arxiv.org/pdf/1502.00804.pdf
 * The optimal for μ appears to have a wide range (500-10000).
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class JelinekMercerSPUD extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }
 
    /**
     * Estimation of the Background DCM (EDCM) via Multivariate Polya Distribution
     *
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
        $lambda = $docUniqueLength/$docLength;
        $mle = $tf/$docLength;

        return log(((1-$lambda) * $mle + $lambda * ($this->getDocumentFrequency()/$this->getUniqueTotalByTermPresence())));

    }

    
}