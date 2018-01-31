<?php

namespace Basset\Ranking;

use Basset\Ranking\ScoringInterface;


/**
 * JelinekMercerLM is a class for ranking documents against a query based on Linear interpolation of the maximum 
 * likelihood model.
 *
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The value for λ is generally very small (0.1) for title queries and around 0.7 for verbose.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class JelinekMercerLM implements ScoringInterface
{

    const LAMBDA = 0.20;

    protected $math;

    protected $lambda;

    public function __construct($lambda = self::LAMBDA)
    {
        $this->lambda = $lambda;

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
            $score += $keyFrequency * log(1 + (((1 - $this->lambda) * $tf) / $docLength) + ($this->lambda * $smoothed_probability));
        }

        return $score;

    }

    
}