<?php

namespace Basset\Ranking;

use Basset\Ranking\ScoringInterface;


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


class DirichletLM implements ScoringInterface
{

    const MU = 2500;

    protected $mu;

    public function __construct($mu = self::MU)
    {
        $this->mu = $mu;

    }
 
    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount)
    {
        $score = 0;

        if($tf != 0){
            $smoothed_probability = $termFrequency / $collectionLength;
            $score += $keyFrequency * log(1 + ($tf + $this->mu * $smoothed_probability) / ($docLength + $this->mu));

        }

        return $score;

    }

    
}