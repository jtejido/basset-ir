<?php

namespace Basset\Ranking;



/**
 * DirichletLM is a class for ranking documents against a query based on Bayesian smoothing with 
 * Dirichlet Prior for language modelling.
 *
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The optimal for μ appears to have a wide range (500-10000).
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DirichletLM extends SimilarityBase implements ScoringInterface
{

    const MU = 2500;

    protected $mu;

    public function __construct($mu = self::MU)
    {
        $this->mu = $mu;

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

            $score += $keyFrequency * log(1 + ($tf + $this->mu * $mle_c) / ($docLength + $this->mu));

        }

        return $score;

    }

    
}