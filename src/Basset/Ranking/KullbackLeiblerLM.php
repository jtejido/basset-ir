<?php

namespace Basset\Ranking;

use Basset\Ranking\ScoringInterface;

/**
 * KullbackLeiblerLM is class that's based on risk minimization thru KL-divergence.
 *
 * The implementation is based on the paper by John Lafferty, Chengxiang Zhai
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.69.116&rep=rep1&type=pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class KullbackLeiblerLM implements ScoringInterface
{

    const MU = 2000;

    protected $mu;

    public function __construct($mu = self::MU)
    {
        $this->mu = $mu;
    }

    /**
     * We'll use pivoted normalized Idf as BM25's Idf.
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount, $keylength)
    {
        $score = 0;

        if($tf != 0){
            $smoothed_probability = $termFrequency / $collectionLength;
            $score += $keyFrequency * log(1 + ($tf/($this->mu * $smoothed_probability)) ) + $keylength * log($this->mu/($docLength + $this->mu) );
        }

        return $score;

    }

}