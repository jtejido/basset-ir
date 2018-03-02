<?php

namespace Basset\Ranking;


/**
 * TwoStageLM is a class for ranking documents that explicitly captures the different influences of the query and document 
 * collection on the optimal settings of retrieval parameters.
 * It involves two steps. Estimate a document language for the model, and Compute the query likelihood using the estimated 
 * language model. (DirichletLM and JelinkedMercerLM)
 *
 * From Chengxiang Zhai and John Lafferty. 2002. Two-Stage Language Models for Information Retrieval.
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.7.3316&rep=rep1&type=pdf
 * 
 * In a nutshell, this is a generalization of JelinkedMercerLM and DirichletLM.
 * The default values used here are the same constants found from the two classes.
 * Thus, making λ = 0 and μ same value as DirichletLM Class resolves the score towards DirichletLM, while making μ = 0 and
 * λ same value as JelinekMercerLM Class resolves the score towards JelinekMercerLM.
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TwoStageLM extends SimilarityBase implements ScoringInterface
{

    const LAMBDA = 0.20;

    const MU = 2500;

    protected $lambda;

    protected $mu;

    public function __construct($lambda = self::LAMBDA, $mu = self::MU)
    {
        $this->mu = $mu;
        $this->lambda = $lambda;
    }
 
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
            // smoothed probability of words seen in the collection
            $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();
            $score += $keyFrequency * log(1 + (((1 - $this->lambda) * ($tf + ($this->mu * $mle_c)) / ($docLength + $this->mu)) + ($this->lambda * $mle_c)));

        }

        return $score;

    }

    
}